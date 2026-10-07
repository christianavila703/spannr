<?php

namespace App\Jobs\FormNotifications;

use App\Models\FormEntry;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Sends a newsletter signup (a FormEntry) to beehiiv.
 *
 * HOW THIS RUNS. Production and staging both use QUEUE_CONNECTION=sync, there is no queue worker and
 * no Horizon. A queued job would therefore run inside the visitor's own page request, and release()
 * or $tries would never retry anything. So this is deliberately NOT a queued job. FormEntry starts it
 * with dispatchAfterResponse(): it runs in Laravel's terminating phase, after PHP-FPM has already sent
 * the response to the browser (Response::send() calls fastcgi_finish_request()). The visitor never
 * waits on beehiiv. If PHP is not running under FPM the response is not finished early and the
 * visitor would wait for this job, which is why the HTTP timeouts below are short.
 *
 * There is no queue-style retry. The one exception is a malformed-request answer: on 400 or 422 the
 * job sends exactly one more request with only the email (see below). Only the FINAL outcome is
 * written to the entry's fields as flat keys (beehiiv_status, see record()), and anything that is not "sent" is retried later by
 * `php artisan beehiiv:backfill --send --retry-recorded` (which runs this same job, so it gets the
 * same email-only retry):
 * beehiiv_status is one of:
 *   sent                   beehiiv answered 2xx
 *   skipped_no_credentials BEEHIIV_API_KEY or BEEHIIV_PUBLICATION_ID is blank
 *   failed                 anything else: 4xx, 429, 5xx, timeout, connection error, unexpected exception
 *
 * handle() never throws, in the request or in the terminating phase.
 * Logs contain only the FormEntry id, the HTTP status and exception class names.
 * Never the key, the email or the request body.
 *
 * Why the email-only retry: beehiiv's docs give no formats for the utm fields or referring_site, so a
 * rejected optional field must never lose a signup. It costs the visitor nothing because this runs
 * after the response has been sent.
 *
 * Worst case time spent here per signup, always after the response: one request normally (2 seconds
 * to connect, 5 seconds in total). Two requests on a 400 or 422, so about 10 seconds at most.
 */
class SubscribeToBeehiiv
{
    use Dispatchable;

    const STATUS_SENT = 'sent';
    const STATUS_SKIPPED = 'skipped_no_credentials';
    const STATUS_FAILED = 'failed';

    private const API_URL = 'https://api.beehiiv.com/v2/publications/%s/subscriptions';
    private const EMAIL_ONLY_RETRY_STATUSES = [400, 422];
    private const CONNECT_TIMEOUT_SECONDS = 2;
    private const TIMEOUT_SECONDS = 5;

    public function __construct(public FormEntry $formEntry)
    {
    }

    public function handle(): void
    {
        try {
            $this->subscribe();
        } catch (Throwable $exception) {
            // Last resort so nothing can escape into the request or the terminating phase.
            $this->log('warning', 'FormEntry ' . $this->formEntry->id . ': beehiiv job error (' . class_basename($exception) . ')');
            $this->record(self::STATUS_FAILED);
        }
    }

    private function subscribe(): void
    {
        $key = config('services.beehiiv.key');
        $publicationId = config('services.beehiiv.publication_id');

        if (blank($key) || blank($publicationId)) {
            $this->record(self::STATUS_SKIPPED);
            $this->log('info', 'FormEntry ' . $this->formEntry->id . ': beehiiv skipped, credentials not configured');

            return;
        }

        $response = $this->send($key, $publicationId, $this->payload());

        // A 400 or 422 may be an optional field beehiiv does not like. Try exactly once more with only the email.
        if ($response && in_array($response->status(), self::EMAIL_ONLY_RETRY_STATUSES, true)) {
            $this->log('info', 'FormEntry ' . $this->formEntry->id . ': beehiiv HTTP ' . $response->status() . ', trying once more with the email only');
            $response = $this->send($key, $publicationId, $this->minimalPayload());
        }

        if ($response === null) {
            $this->record(self::STATUS_FAILED);

            return;
        }

        $status = $response->status();
        $this->log('info', 'FormEntry ' . $this->formEntry->id . ': beehiiv HTTP ' . $status);

        if ($response->successful()) {
            $subscriptionStatus = $response->json('data.status') ?? $response->json('status');
            $this->record(self::STATUS_SENT, $status, is_string($subscriptionStatus) ? $subscriptionStatus : null);

            return;
        }

        // Any other non-2xx, or the final answer after the email-only retry: recorded as failed.
        $this->record(self::STATUS_FAILED, $status);
    }

    private function payload(): array
    {
        $payload = [
            'email' => trim((string) $this->formEntry->email),
            'utm_source' => Str::slug((string) $this->formEntry->form),
            'utm_medium' => 'website',
            'send_welcome_email' => false,
            'reactivate_existing' => false,
        ];

        $url = $this->formEntry->url;

        if (filled($url) && filter_var($url, FILTER_VALIDATE_URL)) {
            $payload['referring_site'] = $url;
        }

        return $payload;
    }

    private function minimalPayload(): array
    {
        return [
            'email' => trim((string) $this->formEntry->email),
            'send_welcome_email' => false,
            'reactivate_existing' => false,
        ];
    }

    private function send(string $key, string $publicationId, array $payload): ?Response
    {
        try {
            return Http::withToken($key)
                ->acceptJson()
                ->asJson()
                ->connectTimeout(self::CONNECT_TIMEOUT_SECONDS)
                ->timeout(self::TIMEOUT_SECONDS)
                ->post(sprintf(self::API_URL, rawurlencode($publicationId)), $payload);
        } catch (Throwable $exception) {
            // Timeout, DNS or connection error. The message can contain the URL, so log the class only.
            $this->log('warning', 'FormEntry ' . $this->formEntry->id . ': beehiiv request failed (' . class_basename($exception) . ')');

            return null;
        }
    }

    /**
     * Logging must never be able to break a signup (a full disk or an unwritable log file would throw).
     */
    private function log(string $level, string $message): void
    {
        try {
            Log::{$level}($message);
        } catch (Throwable $exception) {
            // nothing sensible left to do
        }
    }

    private function record(string $status, ?int $httpStatus = null, ?string $subscriptionStatus = null): void
    {
        try {
            $entry = $this->formEntry->newQuery()->find($this->formEntry->getKey());

            if (! $entry) {
                return;
            }

            // Keep every original key; only add or replace the beehiiv_* keys. They are flat strings on
            // purpose: a nested value inside fields breaks anything that echoes every value (the Funding
            // Applicant "Show all fields" modal does). Recording again replaces them, never nests or duplicates.
            $fields = $entry->fieldsArray();

            foreach (array_keys($fields) as $key) {
                if (str_starts_with((string) $key, 'beehiiv')) {
                    unset($fields[$key]);
                }
            }

            $record = array_filter([
                'status' => $status,
                'http_status' => $httpStatus,
                'subscription_status' => $subscriptionStatus,
                'at' => now()->toIso8601String(),
            ], fn ($value) => $value !== null);

            foreach ($record as $key => $value) {
                $fields['beehiiv_' . $key] = $value;
            }

            $entry->fields = $entry->hasCast('fields') ? $fields : json_encode($fields);
            $entry->save();
        } catch (Throwable $exception) {
            $this->log('warning', 'FormEntry ' . $this->formEntry->id . ': could not record beehiiv status (' . class_basename($exception) . ')');
        }
    }
}
