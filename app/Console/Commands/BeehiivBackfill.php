<?php

namespace App\Console\Commands;

use App\Jobs\FormNotifications\SubscribeToBeehiiv;
use App\Models\FormEntry;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

/**
 * Sends newsletter signups that never reached beehiiv.
 *
 * Both servers use QUEUE_CONNECTION=sync and have no queue worker, so nothing retries by itself.
 * This command is the retry mechanism: it runs the job inline, one request at a time, with a pause
 * between requests. Two modes:
 *
 *   --since=DATE / --all-time   old signups that have no beehiiv record at all (the Mailchimp era).
 *                               --send refuses to run without one of the two.
 *   --retry-recorded            only rows already marked failed or skipped_no_credentials by the job.
 *                               Never touches old Mailchimp-era rows, so it is safe to schedule.
 */
class BeehiivBackfill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'beehiiv:backfill
                            {--send : Actually send to beehiiv. Without this flag nothing is sent (dry run)}
                            {--since= : Only signups created on or after this date, e.g. 2026-10-07 (the cutover date)}
                            {--all-time : With --send, deliberately include every signup ever saved (use --since instead unless you mean it)}
                            {--retry-recorded : Only rows already marked failed or skipped_no_credentials. Cannot be combined with --since or --all-time}
                            {--limit= : Send at most this many emails in this run}
                            {--delay=2.5 : Seconds to wait between requests (2.5 is about 24 per minute)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send newsletter signups that never reached beehiiv. Dry run unless --send is given.';

    private const RETRYABLE_STATUSES = [
        SubscribeToBeehiiv::STATUS_FAILED,
        SubscribeToBeehiiv::STATUS_SKIPPED,
    ];

    public function handle(): int
    {
        $send = (bool) $this->option('send');
        $retryRecorded = (bool) $this->option('retry-recorded');
        $limit = $this->option('limit') !== null ? max(0, (int) $this->option('limit')) : null;
        $delay = max(0.0, (float) $this->option('delay'));
        $since = null;

        if ($retryRecorded && ($this->option('since') !== null || $this->option('all-time'))) {
            $this->error('--retry-recorded cannot be combined with --since or --all-time. Nothing was sent.');

            return self::FAILURE;
        }

        if ($this->option('since') !== null) {
            try {
                $since = Carbon::parse($this->option('since'))->startOfDay();
            } catch (Throwable $exception) {
                $this->error('--since must be a date such as 2026-10-07. Nothing was sent.');

                return self::FAILURE;
            }
        }

        if ($since !== null && $this->option('all-time')) {
            $this->error('Use either --since or --all-time, not both. Nothing was sent.');

            return self::FAILURE;
        }

        if ($send && ! $retryRecorded && $since === null && ! $this->option('all-time')) {
            $this->error('--send needs a limit. Pass --since=DATE (the cutover date), or --all-time only if you really mean every signup ever saved, including people who may have unsubscribed from Mailchimp. Nothing was sent. (To retry rows the job already marked failed or skipped, use --retry-recorded.)');

            return self::FAILURE;
        }

        [$total, $alreadySent, $duplicates, $invalid, $pending] = $this->collect($since, $retryRecorded);

        $this->line('Newsletter signup rows (all newsletter forms): ' . $total);
        $this->line('Already sent to beehiiv: ' . $alreadySent);
        $this->line('Skipped, same email already handled: ' . $duplicates);
        $this->line('Skipped, not a valid email: ' . $invalid);
        $this->line('Would send: ' . count($pending) . ' distinct emails');

        if ($retryRecorded) {
            $this->line('Retry mode: only rows already marked failed or skipped_no_credentials. Rows with no beehiiv record are left alone.');
        } elseif ($since === null) {
            $this->warn('No --since date: this covers every signup ever saved, including people who may have unsubscribed from Mailchimp since. Use --since=<cutover date>. (--send refuses to run without --since or --all-time.)');
        } else {
            $this->line('Only signups created on or after ' . $since->toDateString());
        }

        if (! $send) {
            $this->line('Sample (masked): ' . (collect($pending)->take(5)->map(fn ($row) => $this->mask($row->email))->implode(', ') ?: 'none'));
            $this->line('Estimated time at ' . $delay . 's spacing: about ' . (int) ceil(count($pending) * $delay / 60) . ' minutes');
            $this->info('Dry run only, nothing was sent. Re-run with --send to send.');

            return self::SUCCESS;
        }

        if (blank(config('services.beehiiv.key')) || blank(config('services.beehiiv.publication_id'))) {
            $this->error('BEEHIIV_API_KEY and BEEHIIV_PUBLICATION_ID must be set. Nothing was sent.');

            return self::FAILURE;
        }

        if ($limit !== null) {
            $pending = array_slice($pending, 0, $limit);
        }

        $sent = 0;
        $failed = 0;
        $consecutiveFailures = 0;

        foreach ($pending as $index => $row) {
            // Runs inline: the queue is sync and there is no worker. Each row takes one request (at most 5 seconds),
            // or two on a 400 or 422 (about 10 seconds at most), because it runs the same job.
            SubscribeToBeehiiv::dispatchSync($row);

            $status = $this->beehiivStatus($row->fresh());

            if ($status === SubscribeToBeehiiv::STATUS_SENT) {
                $sent++;
                $consecutiveFailures = 0;
            } else {
                $failed++;
                $consecutiveFailures++;
            }

            if (($index + 1) % 25 === 0) {
                $this->line('Progress: ' . ($index + 1) . ' of ' . count($pending) . ' (sent ' . $sent . ', not sent ' . $failed . ')');
            }

            if ($consecutiveFailures >= 3) {
                $this->error('Stopping after 3 failures in a row (check the key, the publication ID and the rate limit). Re-run later, it resumes where it stopped.');

                break;
            }

            if ($delay > 0 && $index < count($pending) - 1) {
                usleep((int) ($delay * 1000000));
            }
        }

        $this->info('Done. Sent: ' . $sent . ', not sent: ' . $failed . '.');

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }

    /**
     * @return array{0:int,1:int,2:int,3:int,4:array<int,FormEntry>}
     */
    private function collect(?Carbon $since, bool $retryRecorded): array
    {
        $rows = FormEntry::whereIn('form', FormEntry::NEWSLETTER_FORMS)
            ->when($since, fn ($query) => $query->where('created_at', '>=', $since))
            ->orderBy('id')
            ->get(['id', 'form', 'email', 'url', 'fields']);

        $sentEmails = [];

        foreach ($rows as $row) {
            if ($this->beehiivStatus($row) === SubscribeToBeehiiv::STATUS_SENT) {
                $sentEmails[$this->normalize($row->email)] = true;
            }
        }

        $alreadySent = 0;
        $duplicates = 0;
        $invalid = 0;
        $pending = [];
        $queued = [];

        foreach ($rows as $row) {
            $email = $this->normalize($row->email);
            $status = $this->beehiivStatus($row);

            if ($status === SubscribeToBeehiiv::STATUS_SENT) {
                $alreadySent++;
            } elseif ($retryRecorded && ! in_array($status, self::RETRYABLE_STATUSES, true)) {
                continue; // no beehiiv record (Mailchimp era): not this mode's business
            } elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $invalid++;
            } elseif (isset($sentEmails[$email]) || isset($queued[$email])) {
                $duplicates++;
            } else {
                $queued[$email] = true;
                $pending[] = $row;
            }
        }

        return [$rows->count(), $alreadySent, $duplicates, $invalid, $pending];
    }

    private function beehiivStatus(?FormEntry $entry): ?string
    {
        return $entry?->fieldsArray()['beehiiv_status'] ?? null;
    }

    private function normalize(?string $email): string
    {
        return strtolower(trim((string) $email));
    }

    private function mask(?string $email): string
    {
        [$local, $domain] = array_pad(explode('@', (string) $email, 2), 2, '');

        return substr($local, 0, 1) . '***@' . $domain;
    }
}
