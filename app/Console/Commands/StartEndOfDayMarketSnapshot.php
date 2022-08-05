<?php

namespace App\Console\Commands;

use App\Models\MarketSnapshot;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\SlackAlerts\Facades\SlackAlert;

class StartEndOfDayMarketSnapshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapshot:EOD';

    /**
     * The console command description.
     *
     * @var string
     */
//    protected $description = 'Creates end of day Market Snapshot and gets all data via Polygon API for our index';
protected $description = 'Clones the last hourly for the DAILY snapshot to compare to';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // GetEndOfDayMarketSnapshot::dispatch();

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

        SlackAlert::to('index')->message("<@sydney> " . 'EOD Snapshot ' . route('admin.longevity-index.all'));

        return 0;
    }
}
