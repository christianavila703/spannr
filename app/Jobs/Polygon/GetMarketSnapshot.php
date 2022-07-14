<?php

namespace App\Jobs\Polygon;

use App\Models\MarketSnapshot;
use App\Models\Stock;
use App\Models\StockSnapshot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;
use App\Jobs\Polygon\GetTickerDetails;

class GetMarketSnapshot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /* TODO: calc against previous EOD I think */
        $previousMarketSnapshot = MarketSnapshot::daily()->orderByDesc('timestamp')->first();

        $stocks = Stock::active()->get()->pluck('ticker')->toArray();

        $stockList = implode(',', $stocks);

        $marketSnapshot = MarketSnapshot::create([
            'timestamp' => Carbon::now()
        ]);

        $url = 'https://api.polygon.io/v2/snapshot/locale/us/markets/stocks/tickers?tickers=' . $stockList .'&apiKey=iGuwdIWUG9fkFhd9qWEHSbHH4cuFOHxs';

        $batchArray = [];

        try {
            $tickerData = Http::get($url);

            $tickerArray = json_decode($tickerData->body());

            foreach ($tickerArray->tickers as $ticker) {

                $stock = Stock::where('ticker', $ticker->ticker)->first();

                $attributes = [
                    'current_price' => $ticker->day->c,
                    'volume' => $ticker->day->v,
                    'yesterday_close' => $ticker->prevDay->c,
                    'today_high' => $ticker->day->h,
                    'today_low' => $ticker->day->l,
                    'previous_day_change_price' => $ticker->todaysChange,
                    'previous_day_change_percent' => $ticker->todaysChangePerc,
                    'stock_id' => $stock->id,
                    'market_snapshot_id' => $marketSnapshot->id
                ];

                $stockSnapshot = StockSnapshot::create($attributes);

                // Get Market Cap and Outstanding Shares
                array_push($batchArray, new GetTickerDetails($stockSnapshot));

            }

            // Job to Calculate More Stuff
            $batch = Bus::batch($batchArray)
                ->then(function (Batch $batch) use ($marketSnapshot, $previousMarketSnapshot) {
                    // All jobs completed successfully...
                    Log::info('Completed market snapshot successfully');
                    CalculateMarketSnapshotData::dispatch($marketSnapshot, $previousMarketSnapshot);

                })->catch(function (Batch $batch, Throwable $throwable) {
                    // First batch job failure detected...
                    Log::critical('Market Snapshot Batch Failure' . "\n" . $throwable->getMessage());

                })->dispatch();

        } catch(\Throwable $throwable) {
            Log::critical('Failure during GetMarketSnapshot' . "\n" . $throwable->getMessage());
        }
    }
}
