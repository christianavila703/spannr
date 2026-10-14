<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Log;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Stripe\StripeClient;

class SubscriptionController extends Controller
{
    public function cancel() {
        $user = auth()->user();
        $subscription = $user->subscription()->firstOrFail();
        $subscriptionId = $subscription->stripe_subscription_id;

        try {
            $stripe = new StripeClient(config('services.stripe.api_key'));
            $subscriptionData = $stripe->subscriptions->update($subscriptionId, ['cancel_at_period_end' => true]);

            if ($subscriptionData->cancel_at_period_end !== true) {
                throw new \Exception('Stripe response error whe trying to cancel subscription. Subscription Stripe ID: ' . $subscriptionId);
            }

            $subscription->status = UserSubscription::STATUS_CANCELLED;
            $subscription->save();

            $slackMessage = 'User ' . $user->fullName . ' (' . $user->email . ') canceled Spannr+ subscription.';
            SlackAlert::to(UserSubscription::SLACK_NOTIFICATION_CHANNEL)->message($slackMessage);

            return redirect()->route('panel.index')->with('success', 'Your subscription was canceled successfully! Your access will be disabled after the end date.');
        } catch (\Throwable $throwable) {
            $errorMessage = $throwable->getMessage();

            $slackMessage = '*Spannr+ subscription cancellation error*' . "\n" . ">Subscription Stripe ID: " . $subscriptionId . ".\n >Message: " . $errorMessage;
            SlackAlert::to(UserSubscription::SLACK_NOTIFICATION_CHANNEL)->message($slackMessage);

            Log::critical($errorMessage);

            return redirect()->route('panel.index')->with('error', 'An error occurred during the subscription cancellation process. Please contact admin@spannr.com and we’ll get right on it!');
        }
    }

    public function resume() {
        $user = auth()->user();
        $subscription = $user->subscription()->firstOrFail();
        $subscriptionId = $subscription->stripe_subscription_id;

        try {
            $stripe = new StripeClient(config('services.stripe.api_key'));
            $subscriptionData = $stripe->subscriptions->update($subscriptionId, ['cancel_at_period_end' => false]);

            if ($subscriptionData->cancel_at_period_end !== false) {
                throw new \Exception('Stripe response error when trying to resume subscription. Subscription Stripe ID: ' . $subscriptionId);
            }

            $subscription->status = UserSubscription::STATUS_ACTIVE;
            $subscription->save();

            $slackMessage = 'User ' . $user->fullName . ' (' . $user->email . ') resumed Spannr+ subscription.';
            SlackAlert::to(UserSubscription::SLACK_NOTIFICATION_CHANNEL)->message($slackMessage);

            return redirect()->route('panel.index')->with('success', 'Your subscription was resumed successfully!');
        } catch (\Throwable $throwable) {
            $errorMessage = $throwable->getMessage();
            $slackMessage = '*Spannr+ subscription resuming error*' . "\n" . ">Subscription Stripe ID: " . $subscriptionId . ".\n >Message: " . $errorMessage;

            SlackAlert::to(UserSubscription::SLACK_NOTIFICATION_CHANNEL)->message($slackMessage);
            Log::critical($errorMessage);

            return redirect()->route('panel.index')->with('error', 'An error occurred during the subscription resuming process. Please contact admin@spannr.com and we’ll get right on it!');
        }
    }
}
