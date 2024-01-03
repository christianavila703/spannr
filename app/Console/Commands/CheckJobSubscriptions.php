<?php

namespace App\Console\Commands;

use App\Jobs\Subscriptions\CheckJobSubscriptionStatus;
use App\Models\JobSubscription;
use Illuminate\Console\Command;

class CheckJobSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-jobs';

    protected $description = 'Dispatches a job to check active job subscriptions';

    public function handle()
    {
        $jobSubscriptions = JobSubscription::active()->get();

        foreach ($jobSubscriptions as $jobSubscription) {
            CheckJobSubscriptionStatus::dispatch($jobSubscription);
        }
        return Command::SUCCESS;
    }
}
