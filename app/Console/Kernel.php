<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // DEV-19: the server had no "schedule:run" cron line for a long time, so nothing in this list ran. Adding that line
        // starts every ACTIVE command below at once, so the list was trimmed to the commands that are safe and wanted today.
        // The commented out lines are kept so they can be switched back on: remove the leading "//" and run the tests.

        // DEV-19 disabled: backups. A server cron script already does a nightly mysqldump to Google Drive, and backup:clean
        // deletes old backups on the configured disks. To re-enable, uncomment both lines (backup:run also mails sydney@spannr.com).
        // $schedule->command('backup:clean')->daily()->at('02:00');
        // $schedule->command('backup:run')->daily()->at('01:30');

        //Clear outdated tokens
        $schedule->command('magic-link-tokens:clear')->dailyAt('02:30');

        // DEV-19 disabled: Horizon is not in use (QUEUE_CONNECTION=sync, no worker, no Horizon), so this would fail every five
        // minutes looking for Redis. To re-enable, run Horizon on a Redis queue first, then uncomment.
        // $schedule->command('horizon:snapshot')->everyFiveMinutes();

        // DEV-19 disabled: Mailchimp is the old newsletter system (beehiiv replaced it). This re-sends every form entry that
        // has no Mailchimp status (investor opt-ins, funding applicants, report opt-ins) to Mailchimp, which on a first run
        // would be a mass resubmit of old entries. To re-enable, uncomment only if Mailchimp is back in use.
        // $schedule->command('form-entry:resubmit-mailchimp')->dailyAt('17:00');

        // beehiiv: the queue is sync and there is no worker, so nothing else retries a newsletter signup
        // whose beehiiv call failed or was skipped. Only touches rows already marked failed or
        // skipped_no_credentials, never old Mailchimp-era rows. The lock expires after 2 hours.
        $schedule->command('beehiiv:backfill --send --retry-recorded')->hourly()->withoutOverlapping(120);

        $schedule->command('record-locks:clear')->everyMinute();

        // DEV-19 disabled, NEEDS A DECISION: subscription:check is the Spannr+ renewal check (not the jobs boards). For every
        // subscription whose end date has passed it asks Stripe for the current state and updates the row, and posts a Slack
        // alert on any error. Nothing else refreshes Spannr+ subscriptions (the Stripe webhooks only handle setup intents and
        // checkout), so while this is off a cancelled subscriber can keep access. A first run would also check every old expired
        // subscription at once. To re-enable, uncomment after checking the Stripe key and the Slack channel.
        // $schedule->command('subscription:check')->dailyAt('03:00');

        // DEV-19 disabled: the jobs boards are retired. This asks Stripe about every active job subscription and, for one that
        // is no longer active, posts a Slack alert and clears the job's monthly flag. It also looks the subscription up with
        // the local row id instead of the Stripe id, so it fails. To re-enable, fix that lookup first, then uncomment.
        // $schedule->command('subscriptions:check-jobs')->dailyAt('3:05');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
