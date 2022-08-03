<?php

namespace App\Console\Commands;

use App\Jobs\WordPress\SyncGlossary;
use Illuminate\Console\Command;

class WordPressSyncGlossary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wordpress:sync-glossary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all glossary items from WordPress';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        SyncGlossary::dispatch();
        return 0;
    }
}
