<?php

namespace App\Jobs\Subscriptions;

use App\Models\Job;
use App\Models\JobSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Stripe\StripeClient;

class CheckJobSubscriptionStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public JobSubscription $jobSubscription;

    public function __construct(JobSubscription $jobSubscription)
    {
        $this->jobSubscription = $jobSubscription;
    }

    public function handle()
    {
        $stripe = new StripeClient(config("services.stripe.api_key"));

        $subscriptionDetails = $stripe->subscriptions->retrieve($this->jobSubscription->id);

        $cardDetails = $stripe->paymentMethods->retrieve(
            $subscriptionDetails["default_payment_method"]
        );

        $this->jobSubscription->data = $subscriptionDetails;
        $this->jobSubscription->status = $subscriptionDetails['status'];
        $this->jobSubscription->payment_method = $subscriptionDetails['payment_method'];
        $this->jobSubscription->current_period_end = $subscriptionDetails['current_period_end'];
        $this->jobSubscription->current_period_start = $subscriptionDetails['current_period_start'];
        $this->jobSubscription->last4 = $cardDetails["card"]["last4"];
        $this->jobSubscription->brand = $cardDetails["card"]["brand"];
        $this->jobSubscription->exp_year = $cardDetails["card"]["exp_year"];
        $this->jobSubscription->exp_month = $cardDetails["card"]["exp_month"];
        $this->jobSubscription->save();

        if ($subscriptionDetails['status'] !== 'active') {
            SlackAlert::to('test')->message('<@sydney> Job Subscription is no longer active ' . $this->jobSubscription->id);
            $job = Job::find($this->jobSubscription->job_id);
            $job->monthly = null;
            $job->save();
        }
    }
}
