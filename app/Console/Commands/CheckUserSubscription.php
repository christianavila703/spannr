<?php

namespace App\Console\Commands;

use App\Jobs\Subscriptions\UpdateExpiredSubscription;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckUserSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Check and update status for expired user subscriptions.";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expiredSubscriptions = UserSubscription::where('end_date', '<', Carbon::now())->get();

        foreach ($expiredSubscriptions as $subscription) {
            UpdateExpiredSubscription::dispatch($subscription);
        }
    }
}
