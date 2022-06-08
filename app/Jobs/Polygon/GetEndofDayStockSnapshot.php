<?php

namespace App\Jobs\Polygon;

use App\Models\MarketSnapshot;
use App\Models\Stock;
use App\Models\StockSnapshot;
use Carbon\Traits\Date;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class GetEndofDayStockSnapshot implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Stock $stock;
    protected MarketSnapshot $marketSnapshot;
    protected string $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Stock $stock, MarketSnapshot $marketSnapshot, string $date)
    {
        $this->stock = $stock;
        $this->marketSnapshot = $marketSnapshot;
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...
            return;
        }

        $url = 'https://api.polygon.io/v1/open-close/' . $this->stock->ticker .'/' . $this->date . '?adjusted=true&apiKey=iGuwdIWUG9fkFhd9qWEHSbHH4cuFOHxs';

        $request = Http::get($url);

        $response = json_decode($request->body());

        $stockSnapshot = new StockSnapshot();
        $stockSnapshot->current_price = $response->close;
        $stockSnapshot->volume = $response->volume;
        $stockSnapshot->today_high = $response->high;
        $stockSnapshot->today_low = $response->low;
        $stockSnapshot->market_snapshot_id = $this->marketSnapshot->id;
        $stockSnapshot->stock_id = $this->stock->id;
        $stockSnapshot->save();

        // GET Market Cap for each Stock
        $url = 'https://api.polygon.io/v3/reference/tickers/' . $this->stock->ticker . '?date=' . $this->date . '&apiKey=iGuwdIWUG9fkFhd9qWEHSbHH4cuFOHxs';

        $request = Http::get($url);

        $response = json_decode($request->body());

        $stockSnapshot->market_cap = $response->results->market_cap;
        $stockSnapshot->outstanding_shares = $response->results->share_class_shares_outstanding;
        $stockSnapshot->save();
    }
}
