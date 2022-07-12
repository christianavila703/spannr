<?php

namespace App\Console\Commands;

use App\Jobs\Polygon\GetEndOfDayMarketSnapshot;
use Illuminate\Console\Command;

class StartEndOfDayMarketSnapshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapshot:EOD';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates end of day Market Snapshot and gets all data via Polygon API for our index';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        GetEndOfDayMarketSnapshot::dispatch();
        return 0;
    }
}
