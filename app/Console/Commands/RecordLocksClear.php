<?php

namespace App\Console\Commands;

use App\Models\RecordLock;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RecordLocksClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'record-locks:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear outdated record locks.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $delay = config('record-locks.delay');

        RecordLock::where('created_at', '<', Carbon::now()->subMinutes($delay))->delete();
    }
}
