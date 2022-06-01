<?php

namespace App\Jobs\Polygon;

use App\Models\MarketSnapshot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateMarketSnapshotData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private MarketSnapshot $marketSnapshot;
    private MarketSnapshot $previousMarketSnapshot;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MarketSnapshot $marketSnapshot, MarketSnapshot $previousMarketSnapshot)
    {
        $this->marketSnapshot = $marketSnapshot;
        $this->previousMarketSnapshot = $previousMarketSnapshot;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $stockSnapshots = $this->marketSnapshot->stockSnapshots();

        $marketCapSum = $stockSnapshots->sum('market_cap');

        // Calcs against base MC
        $indexChange = ($marketCapSum / $this->marketSnapshot->base_mc) - 1;
        $indexValue = 100 * (1 + $indexChange);

        $this->marketSnapshot->market_cap = $marketCapSum;
        $this->marketSnapshot->index_change = $indexChange;
        $this->marketSnapshot->index_value = $indexValue;

        // Calcs against previous snapshot
        $dailyIndexChange = ($marketCapSum / $this->previousMarketSnapshot->market_cap) - 1;
        $dailyIndexValue = 100 * (1 + $dailyIndexChange);

        $this->marketSnapshot->daily_index_change = $dailyIndexChange;
        $this->marketSnapshot->daily_index_value = $dailyIndexValue;

        $this->marketSnapshot->save();

        CalculateStockWeight::dispatch($this->marketSnapshot);

    }
}
