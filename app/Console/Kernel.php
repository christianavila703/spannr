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
        // Backups
        $schedule->command('backup:clean')->daily()->at('02:00');
        $schedule->command('backup:run')->daily()->at('01:30');

        //Clear outdated tokens
        $schedule->command('magic-link-tokens:clear')->dailyAt('02:30');

        // Horizon
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        $schedule->command('form-entry:resubmit-mailchimp')->dailyAt('17:00');

        $schedule->command('record-locks:clear')->everyMinute();

        // Subscriptions
        $schedule->command('subscription:check')->dailyAt('03:00');
        $schedule->command('subscriptions:check-jobs')->dailyAt('3:05');
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
