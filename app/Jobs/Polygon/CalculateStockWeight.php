<?php

namespace App\Jobs\Polygon;

use App\Models\MarketSnapshot;
use App\Models\StockSnapshot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateStockWeight implements ShouldQueue
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
        $stockSnapshots = $this->marketSnapshot->stockSnapshots;

        foreach($stockSnapshots as $snapshot) {
            $snapshot->weight = round($snapshot->market_cap / $this->marketSnapshot->market_cap, 4) * 100;
            $snapshot->save();
        }

        $this->marketSnapshot->status = MarketSnapshot::STATUS_COMPLETE;
        $this->marketSnapshot->save();
    }
}
