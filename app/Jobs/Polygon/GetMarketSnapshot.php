<?php

namespace App\Jobs\Polygon;

use App\Jobs\Polygon\GetTickerDetails;
use App\Models\MarketSnapshot;
use App\Models\Stock;
use App\Models\StockSnapshot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Throwable;

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
        $previousMarketSnapshot = MarketSnapshot::daily()->complete()->orderByDesc('timestamp')->first();

        $stocks = Stock::active()->indexed()->get()->pluck('ticker', 'id')->toArray();

        $stockCount = count($stocks);
        $successfulTickerCount = 0;
        $successfulTickerList = [];

        $stockList = implode(',', $stocks);

        $marketSnapshot = MarketSnapshot::create([
            'timestamp' => Carbon::now()
        ]);

        $url = 'https://api.polygon.io/v1/summaries?ticker.any_of=' . $stockList .'&apiKey=' . config('services.polygon.key');

        $batchArray = [];

        try {
            $tickerData = Http::withHeaders([
                'X-Polygon-Edge-ID' => config('services.polygon.edge-id'),
                'X-Polygon-Edge-IP-Address' => config('services.polygon.edge-ip'),
                'X-Polygon-Edge-User-Agent' => config('services.polygon.edge-user-agent')
            ])->get($url);

            $results = json_decode($tickerData->body());
            $tickers = $results->results;
            $successfulTickerCount = count($tickers);

            Log::info(print_r($tickers, true));

            foreach ($tickers as $ticker) {

                $stock = Stock::where('ticker', $ticker->ticker)->first();
                $successfulTickerList[$stock->id] = $stock->ticker;

                try {
                    $attributes = [
                        'current_price' => $ticker->price,
                        'volume' => $ticker->session->volume,
                        'yesterday_close' => $ticker->session->previous_close,
                        'today_high' => $ticker->session->high,
                        'today_low' => $ticker->session->low,
                        'previous_day_change_price' => $ticker->session->change,
                        'previous_day_change_percent' => $ticker->session->change_percent,
                        'stock_id' => $stock->id,
                        'market_snapshot_id' => $marketSnapshot->id
                    ];

                    $stockSnapshot = StockSnapshot::create($attributes);

                    // Get Market Cap and Outstanding Shares
                    array_push($batchArray, new GetTickerDetails($stockSnapshot));
                } catch(Throwable $exception) {
                    Log::warning('Exception during GetMarketSnapshot for ' . $ticker->ticker . ' ' . $exception->getMessage() . print_r($ticker, true));
                    SlackAlert::to('index')->message('*Exception during GetMarketSnapshot for ' . $ticker->ticker . '*' . "\n" . '`' . $exception->getMessage() . '`' . "\n" . "```" . print_r($ticker, true) . "```");

                    $stockSnapshot = StockSnapshot::orderByDesc('created_at')->where('stock_id', $stock->id)->where('current_price', '>', 0)->first();
                    $newSnapshot = $stockSnapshot->replicate();
                    $newSnapshot->market_snapshot_id = $marketSnapshot->id;
                    $newSnapshot->save();
                }

            }

            // Job to Calculate More Stuff
            $batch = Bus::batch($batchArray)
                ->then(function (Batch $batch) use ($marketSnapshot, $previousMarketSnapshot) {
                    // All jobs completed successfully...
                    Log::info('Completed market snapshot successfully');
                    CalculateMarketSnapshotData::dispatch($marketSnapshot, $previousMarketSnapshot);

                })->catch(function (Batch $batch, Throwable $throwable) {
                    // First batch job failure detected...
                    $message = 'Market Snapshot Batch Failure' . "\n" . $throwable->getMessage();
                    Log::critical($message);
                    SlackAlert::to('index')->message("<@sydney> " . $message);

                })->dispatch();

        } catch(\Throwable $throwable) {
            $message = 'Failure during GetMarketSnapshot' . "\n" . $throwable->getMessage();
            Log::critical($message);
            SlackAlert::to('index')->message("<@sydney> " . $message);
        }

        // Missing Tickers from Polygon
        if ($stockCount !== $successfulTickerCount) {

            $missingTickers = array_diff($stocks, $successfulTickerList);

            $message = 'Missing tickers from hourly snapshot:' . "\n";
            $message .= implode("\n", $missingTickers);

            foreach ($missingTickers as $missingTicker) {
                // find last one & clone it
                $stockId = array_search($missingTicker, $stocks);
                $stockSnapshot = StockSnapshot::orderByDesc('created_at')->where('stock_id', $stockId)->where('current_price', '>', 0)->first();
                $newSnapshot = $stockSnapshot->replicate();
                $newSnapshot->market_snapshot_id = $marketSnapshot->id;
                $newSnapshot->save();
                Log::info('Created clone ', $newSnapshot->toArray());
            }

            CalculateMarketSnapshotData::dispatch($marketSnapshot, $previousMarketSnapshot);
            SlackAlert::to('index')->message("<@sydney> " . $message);
        }
    }
}
