<?php

namespace App\Console\Commands;

use App\Helpers\Polygon\CheckMarketHolidays;
use App\Models\MarketSnapshot;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\SlackAlerts\Facades\SlackAlert;

class StartEndOfDayMarketSnapshot extends Command
{
    protected $signature = 'snapshot:EOD';

    protected $description = 'Clones the last hourly for the DAILY snapshot to compare to';

    public function handle(): int
    {
        $isHoliday = CheckMarketHolidays::checkHolidayStatus(Carbon::now());

        if ($isHoliday === true) {
            return 0;
        }

        $snapshot = MarketSnapshot::orderByDesc('timestamp')->hourly()->with('stockSnapshots')->first();

        $newTime = Carbon::parse($snapshot->timestamp)->format('Y-m-d');
        $newTimeMaybe = Carbon::parse($newTime . ' 5pm', 'America/Chicago');

        $newSnapshot = $snapshot->replicate();
        $newSnapshot->type = 'daily';
        $newSnapshot->timestamp = $newTimeMaybe;
        $newSnapshot->save();

        foreach ($snapshot->stockSnapshots as $stockSnapshot) {
            $newStockSnapshot = $stockSnapshot->replicate();
            $newStockSnapshot->market_snapshot_id = $newSnapshot->id;
            $newStockSnapshot->save();
        }

        SlackAlert::to('index')->message('EOD Snapshot ' . route('admin.longevity-index.all'));

        return 0;
    }
}
