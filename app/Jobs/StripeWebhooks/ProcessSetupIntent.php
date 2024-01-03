<?php

namespace App\Jobs\StripeWebhooks;

use App\Models\JobSubscription;
use App\Models\SetupIntent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\StripeClient;
use Throwable;

class ProcessSetupIntent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public WebhookCall $webhookCall;

    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    public function handle()
    {
        $setupIntentData = $this->webhookCall->payload["data"]["object"];

        $setupIntent = SetupIntent::updateOrCreate(
            ['id' => $setupIntentData['id']],
            [
                'id' => $setupIntentData['id'],
                'status' => $setupIntentData['status'],
                'data' => $setupIntentData,
                'type' => $setupIntentData['metadata']['subscription_type'],
                'subscription_id' => $setupIntentData['metadata']['subscription_id'],
                'job_id' => $setupIntentData['metadata']['job_id'] ?? NULL,
                'user_id' => $setupIntentData['metadata']['user_id'] ?? NULL
            ]
        );

        if ($setupIntentData['metadata']['subscription_type'] === SetupIntent::TYPE_JOB_SUBSCRIPTION) {
            $this->processJobSubscription($setupIntent, $setupIntentData);
        }
    }

    private function processJobSubscription($setupIntent, $setupIntentData) {
        try {
            $jobSubscription = JobSubscription::updateOrCreate(
                ['id' => $setupIntentData['metadata']['subscription_id']],
                [
                    'id' => $setupIntentData['metadata']['subscription_id'],
                    'job_id' => $setupIntentData['metadata']['job_id'],
                    'user_id' => $setupIntentData['metadata']['user_id'],
                    'data' => $setupIntentData,
                ]
            );

            $setupIntent->job_subscription_id = $jobSubscription->id;
            $setupIntent->save();

            if ($setupIntentData['status'] === SetupIntent::STATUS_SUCCEEDED) {

                $stripe = new StripeClient(config("services.stripe.api_key"));

                $subscriptionDetails = $stripe->subscriptions->update($jobSubscription->id, [
                    'default_payment_method' => $setupIntentData['payment_method']
                ]);

                $jobSubscription->status = $subscriptionDetails['status'];
                $jobSubscription->payment_method = $setupIntentData['payment_method'];
                $jobSubscription->data = $subscriptionDetails;
                $jobSubscription->current_period_start = $subscriptionDetails['current_period_start'];
                $jobSubscription->current_period_end = $subscriptionDetails['current_period_end'];
                $jobSubscription->created_at = $subscriptionDetails['start_date'];

                $cardDetails = $stripe->paymentMethods->retrieve($setupIntentData['payment_method']);

                $jobSubscription->last4 = $cardDetails['card']['last4'];
                $jobSubscription->brand = $cardDetails['card']['brand'];
                $jobSubscription->exp_year = $cardDetails['card']['exp_year'];
                $jobSubscription->exp_month = $cardDetails['card']['exp_month'];
                $jobSubscription->save();
            }

        } catch (Throwable $exception) {
            SlackAlert::to('test')->message('<@sydney> Exception during ProcessSetupIntent for Job Subscription Webhook ' . $this->webhookCall->id . "\n" . $exception->getMessage());
        }
    }
}
