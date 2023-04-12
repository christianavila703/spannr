<?php

namespace App\Console\Commands;

use App\Models\MagicLinkToken;
use App\Models\RecordLock;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TokensClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magic-link-tokens:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear outdated magic link tokens.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        MagicLinkToken::where('expires_at', '<', Carbon::now()->subDay())->delete();
    }
}
