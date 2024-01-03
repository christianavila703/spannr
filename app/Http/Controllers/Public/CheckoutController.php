<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Stripe\StripeClient;
use MailchimpMarketing;

class CheckoutController extends Controller
{
    public function eventConfirmation(Request $request) {
        $checkoutId = $request->input('session');

        echo $checkoutId . "\n\n";

        $stripe = new StripeClient(config('services.stripe.api_key'));
        $checkoutDetails = $stripe->checkout->sessions->retrieve($checkoutId);

        if ($checkoutDetails->payment_status !== 'paid') {
            exit('oops - looks unpaid');
//            return redirect()->route('stripe-checkout.confirmation.unpaid');
        }

        $email = $checkoutDetails->customer_details['email'];
        $name = $checkoutDetails->customer_details['name'];
        $phone = $checkoutDetails->customer_details['phone'];

        $subscribeArray = [
            "email_address" => $email,
            "status" => "subscribed",
            "tags" => ["SD Event Buyer"],
            "merge_fields" => [
                "FNAME" => $name,
                "PHONE" => $phone,
            ]
        ];

        $mailchimp = new MailchimpMarketing\ApiClient();

        $mailchimp->setConfig([
          'apiKey' => config('services.mailchimp.key'),
          'server' => config('services.mailchimp.server')
        ]);

        try {
            $mailchimp->lists->addListMember("1dc3f748f8", $subscribeArray);
        } catch (ClientException $exception) {
            try {
                $subscribeArray = [
                    "email_address" => $email,
                    "status" => "subscribed",
                    "tags" => ["SD Event Buyer"],
                ];
                $mailchimp->lists->setListMember("1dc3f748f8", $email, $subscribeArray);
            } catch (ClientException $exception) {
                $errorMessage = $exception->getResponse()->getBody()->getContents();
                Log::critical('Error during Send to MailChimp for Event Confirmation', $errorMessage);
                $message = '*Error during Send to MailChimp for Event Confirmation*' . "\n" .
                    ">Checkout ID " . $checkoutId;
                SlackAlert::to('test')->message($message);
            }
        }

        return redirect()->route('public.content.events.confirmed');
    }

    public function jobPostingSubscription(Request $request)
    {
        try {
            $checkoutId = $request->input('checkout');
            $stripe = new StripeClient(config('services.stripe.api_key'));
            $checkoutDetails = $stripe->checkout->sessions->retrieve($checkoutId);

            if ($checkoutDetails->payment_status !== 'paid' || empty($checkoutDetails->subscription)) {
                throw new \Exception('There was error with your payment. Please contact admin@spannr.com and we’ll get right on it!');
            }

            $job = Job::findOrFail($checkoutDetails->client_reference_id);
            $job->subscription_id = $checkoutDetails->subscription;
            $job->save();

            return redirect()->route('public.content.jobs.subscription.confirmed');
        } catch (\Throwable $throwable) {
            $errorMessage = $throwable->getMessage();

            Log::critical('Error during checkout for Job posting subscription ' . $errorMessage);
            $message = '<@Sydney> *Error during checkout for Job Posting subscription*' . "\n" .
                ">Checkout ID: " . $checkoutId;

            SlackAlert::to('jobs')->message($message);

            return redirect()->route('public.content.jobs.index')->with('error', $errorMessage);
        }
    }

    public function spannrPlusSubscription(Request $request)
    {
        try {
            $checkoutId = $request->input('checkout');
            $stripe = new StripeClient(config('services.stripe.api_key'));
            $checkoutDetails = $stripe->checkout->sessions->retrieve($checkoutId);

            if ($checkoutDetails->payment_status !== 'paid' || empty($checkoutDetails->subscription)
                || empty($checkoutDetails->client_reference_id)) {
                throw new \Exception('An error occurred when the user tried to subscribe to Spannr+.'
                    . ' Stripe status returned: ' . $checkoutDetails->status
                    . '. Subscription ID: ' . $checkoutDetails->subscription
                    . '. User ID: ' . $checkoutDetails->client_reference_id);
            }

            $user = User::findOrFail($checkoutDetails->client_reference_id);
            $user->subscription()->delete();

            $stripeSubscriptionId = $checkoutDetails->subscription;
            $subscriptionDetails = $stripe->subscriptions->retrieve($stripeSubscriptionId);

            $stripePaymentMethodId = $subscriptionDetails->default_payment_method;
            $paymentMethodDetails = $stripe->paymentMethods->retrieve($stripePaymentMethodId);

            $subscription = new UserSubscription();
            $subscription->user_id = $user->id;
            $subscription->status = UserSubscription::STATUS_ACTIVE;
            $subscription->stripe_subscription_id = $stripeSubscriptionId;
            $subscription->card_number = $paymentMethodDetails->card->last4;
            $subscription->start_date = Carbon::parse($subscriptionDetails->current_period_start)->toDate();
            $subscription->end_date = Carbon::parse($subscriptionDetails->current_period_end)->toDate();
            $subscription->save();

            $slackMessage = 'User ' . $user->fullName . ' (' . $user->email . ') subscribed to Spannr+.';

            SlackAlert::to(UserSubscription::SLACK_NOTIFICATION_CHANNEL)->message($slackMessage);

            return redirect()->route('panel.index')->with('success', 'Thank you for Spannr+ subscription!');
        } catch (\Throwable $throwable) {
            $errorMessage = $throwable->getMessage();
            $message = '<@Sydney> *Error during checkout for Spannr+ subscription* \n >Checkout ID: ' . $checkoutId;

            SlackAlert::to(UserSubscription::SLACK_NOTIFICATION_CHANNEL)->message($message);
            Log::critical($errorMessage);

            return redirect()->route('panel.index')->with('error', 'An error occurred during the payment process. Please contact admin@spannr.com and we’ll get right on it!');
        }
    }
}
