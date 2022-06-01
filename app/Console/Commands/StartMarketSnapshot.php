<?php

namespace App\Console\Commands;

use App\Jobs\Polygon\GetMarketSnapshot;
use Illuminate\Console\Command;

class StartMarketSnapshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'startMarketSnapshot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a Market Snapshot and gets all data via Polygon API for our index';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        GetMarketSnapshot::dispatch();
        return 0;
    }
}
