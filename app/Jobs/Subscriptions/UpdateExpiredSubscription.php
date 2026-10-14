<?php

namespace App\Jobs\Subscriptions;

use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Stripe\StripeClient;

class UpdateExpiredSubscription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected UserSubscription $subscription;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UserSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $subscription = $this->subscription;

        try {
            $stripe = new StripeClient(config('services.stripe.api_key'));
            $subscriptionDetails = $stripe->subscriptions->retrieve($subscription->stripe_subscription_id);

            if ($subscriptionDetails && $subscriptionDetails->status == 'active') {
                $subscription->status = UserSubscription::STATUS_ACTIVE;
                $subscription->start_date = Carbon::parse($subscriptionDetails->current_period_start)->toDate();
                $subscription->end_date = Carbon::parse($subscriptionDetails->current_period_end)->toDate();
            } else {
                $subscription->status = UserSubscription::STATUS_INACTIVE;
            }

            $subscription->save();
        } catch (\Throwable $throwable) {
            $errorTitle = 'UpdateExpiredSubscription job error.';
            $errorMessage = $throwable->getMessage();
            $slackMessage = '*' . $errorTitle . '*' . "\n" .
                ">Subscription Stripe ID: " . $subscription->stripe_subscription_id . "\n" .
                ">Message: " . $errorMessage;

            SlackAlert::to('website')->message($slackMessage);
            Log::critical($errorTitle . ' ' . $errorMessage);
        }
    }
}
