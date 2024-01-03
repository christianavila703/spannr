<?php

namespace App\Jobs\StripeWebhooks;

use App\Jobs\Subscriptions\CheckJobSubscriptionStatus;
use App\Models\Job;
use App\Models\JobSubscription;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\StripeClient;

class CheckoutSessionCompleted implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public WebhookCall $webhookCall;

    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    public function handle()
    {
        $checkoutData = $this->webhookCall->payload["data"]["object"];

        $subscriptionId = $checkoutData["subscription"];
        $jobId = $checkoutData["client_reference_id"];

        $job = Job::findOrfail($jobId);
        $job->subscription_id = $subscriptionId;
        $job->save();

        $job_manager_user_id = $job->managers->first()->id ?? null;

        if ($job_manager_user_id) {
            $user = User::find($job_manager_user_id);
            $user->stripe_customer_id = $checkoutData['customer'];
            $user->save();
        }

        $stripe = new StripeClient(config("services.stripe.api_key"));

        $subscriptionDetails = $stripe->subscriptions->retrieve($subscriptionId);

        $jobSubscription = JobSubscription::updateOrCreate(
            ["id" => $subscriptionDetails["id"]],
            [
                "id" => $subscriptionDetails["id"],
                "job_id" => $job->id,
                "user_id" => $job_manager_user_id,
                "status" => $subscriptionDetails["status"],
                "payment_method" => $subscriptionDetails["payment_method"],
                "current_period_end" => $subscriptionDetails["current_period_end"],
                "current_period_start" => $subscriptionDetails["current_period_start"],
                "data" => $subscriptionDetails
            ]
        );

        CheckJobSubscriptionStatus::dispatch($jobSubscription);
    }
}
