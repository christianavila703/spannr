<?php

namespace App\Console\Commands;

use App\Helpers\Polygon\CheckMarketHolidays;
use App\Jobs\Polygon\GetMarketSnapshot;
use Carbon\Carbon;
use Illuminate\Console\Command;

class StartHourlyMarketSnapshot extends Command
{
    protected $signature = 'snapshot:hourly';

    protected $description = 'Creates hourly Market Snapshot and gets all data via Polygon API for our index';

    public function handle(): int
    {
        $isHoliday = CheckMarketHolidays::checkHolidayStatus(Carbon::now());

        if ($isHoliday === true) {
            return 0;
        }

        GetMarketSnapshot::dispatch();
        return 0;
    }
}
