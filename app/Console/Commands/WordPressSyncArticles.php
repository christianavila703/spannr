<?php

namespace App\Console\Commands;

use App\Jobs\WordPress\SyncArticles;
use Illuminate\Console\Command;

class WordPressSyncArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wordpress:sync-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all articles from WordPress';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        SyncArticles::dispatch();
        return 0;
    }
}
