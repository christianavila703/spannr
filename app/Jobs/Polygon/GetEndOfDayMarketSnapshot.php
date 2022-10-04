<?php

namespace App\Jobs\Polygon;

use App\Models\MarketSnapshot;
use App\Models\Stock;
use App\Models\StockSnapshot;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GetEndOfDayMarketSnapshot implements ShouldQueue
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
        $previousSnapshot = MarketSnapshot::daily()->complete()->orderByDesc('timestamp')->first();

        $date = Carbon::parse($previousSnapshot->timestamp)->addWeekdays(1)->format('Y-m-d');
        $dateTime = Carbon::parse($previousSnapshot->timestamp)->addWeekdays(1)->format('Y-m-d H:i:s');

        $marketSnapshot = new MarketSnapshot();
        $marketSnapshot->timestamp = Carbon::parse($dateTime);
        $marketSnapshot->type = MarketSnapshot::TYPE_DAILY;
        $marketSnapshot->save();

        $stocks = Stock::active()->where('date_added', '<', $date)->get();

        $batchArray = [];

        foreach ($stocks as $stock) {
            array_push($batchArray, new GetEndofDayStockSnapshot($stock, $marketSnapshot, $date));
        }

        try {
            $batch = Bus::batch($batchArray)
                ->then(function (Batch $batch) use ($marketSnapshot, $previousSnapshot) {

                    // All jobs completed successfully...
                    Log::info('Completed market snapshot successfully');
                    CalculateMarketSnapshotData::dispatch($marketSnapshot, $previousSnapshot);

                })->catch(function (Batch $batch, Throwable $throwable) {

                    // First batch job failure detected...
                    Log::critical('Market Snapshot Batch Failure' . "\n" . $throwable->getMessage());

                })->dispatch();
        } catch (Throwable $throwable) {
            Log::critical('Error during GetEndOfDayMarketSnapshot ' . "\n" . $throwable->getMessage());
        }
    }
}
