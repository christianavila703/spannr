<?php

namespace App\Jobs\Polygon;

use App\Models\MarketSnapshot;
use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class DailyOpenCloseMarketSnapshot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $stocks = Stock::indexed()->pluck('ticker')->toArray();

        $stockList = implode(',', $stocks);

        $marketSnapshot = MarketSnapshot::create([
            'timestamp' => Carbon::now(),
            'type' => MarketSnapshot::TYPE_DAILY
        ]);

        $url = 'https://api.polygon.io/v2/snapshot/locale/us/markets/stocks/tickers?tickers=' . $stockList .'&apiKey=' . config('services.polygon.key');

        $batchArray = [];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
