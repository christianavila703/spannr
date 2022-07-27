<?php

namespace App\Jobs\Polygon;

use App\Models\StockSnapshot;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\SlackAlerts\Facades\SlackAlert;

class GetTickerDetails implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private StockSnapshot $stockSnapshot;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(StockSnapshot $stockSnapshot)
    {
        $this->stockSnapshot = $stockSnapshot;
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

        $url = 'https://api.polygon.io/v3/reference/tickers/' . $this->stockSnapshot->stock->ticker . '?apiKey=' . config('services.polygon.key');

        $tickerDetails = Http::get($url);
        $tickerDetailsArray = json_decode($tickerDetails->body());

        try {
            $this->stockSnapshot->market_cap = $tickerDetailsArray->results->market_cap;
            $this->stockSnapshot->outstanding_shares = $tickerDetailsArray->results->share_class_shares_outstanding;
            $this->stockSnapshot->save();
        } catch (\Throwable $throwable) {
            $message = 'Failure during GetTickerDetails ' . $this->stockSnapshot->stock->ticker . "\n" . $throwable->getMessage() . "\n" . '```' . print_r($tickerDetailsArray, true) . '```';
            Log::critical($message);
            SlackAlert::to('index')->message("<@sydney> " . $message);
        }
    }
}
