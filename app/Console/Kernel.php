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
        // Market Snapshots
        $schedule->command('snapshot:EOD')
            ->weekdays()
            ->dailyAt('1:00');

        $schedule->command('snapshot:hourly')
            ->weekdays()
            ->everyFifteenMinutes()
            ->between('8:45', '15:30');

        $schedule->command('stocks:get-aggregates')
            ->weekdays()
            ->everyFifteenMinutes()
            ->between('8:45', '15:30');

        $schedule->command('wordpress:sync-articles')
            ->hourlyAt(15);

        $schedule->command('wordpress:sync-glossary')
            ->hourlyAt(20);

        $schedule->command('polygon:get-news')
            ->hourlyAt(25);

        // Backups
        $schedule->command('backup:clean')->daily()->at('02:00');
        $schedule->command('backup:run')->daily()->at('01:30');

        // Horizon
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
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
