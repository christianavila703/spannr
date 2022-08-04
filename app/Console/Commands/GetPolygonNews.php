<?php

namespace App\Console\Commands;

use App\Jobs\Polygon\GetNews;
use App\Models\Organization;
use Illuminate\Console\Command;

class GetPolygonNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'polygon:get-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets news from Polygon for all stocks in the index';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $organizations = Organization::whereHas('stock')->get();

        foreach ($organizations as $organization) {
            GetNews::dispatch($organization);
        }

        return 0;
    }
}
