<?php

namespace App\Console\Commands;

use App\Jobs\Polygon\GetOrganizationStockChart;
use App\Models\Stock;
use Illuminate\Console\Command;

class GetStockAggregates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stocks:get-aggregates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $stocks = Stock::indexed()->get();

        foreach ($stocks as $stock) {
            GetOrganizationStockChart::dispatch($stock);
        }
        return 0;
    }
}
