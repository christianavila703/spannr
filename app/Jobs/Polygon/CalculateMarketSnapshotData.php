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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MarketSnapshot $marketSnapshot)
    {
        $this->marketSnapshot = $marketSnapshot;
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
        $indexChange = ($marketCapSum / $this->marketSnapshot->base_mc) - 1;
        $indexValue = 100 * (1 + $indexChange);

        $this->marketSnapshot->market_cap = $marketCapSum;
        $this->marketSnapshot->index_change = $indexChange;
        $this->marketSnapshot->index_value = $indexValue;

        $this->marketSnapshot->save();
    }
}
