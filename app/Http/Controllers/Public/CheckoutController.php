<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\FormEntry;
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

            if ($checkoutDetails->payment_status !== 'paid') {
                throw new \Exception('There was error with your payment. Please contact admin@spannr.com and we’ll get right on it!');
            }

            return redirect()->route('public.content.jobs.subscription.confirmed');
        } catch (\Throwable $throwable) {
            $errorMessage = $throwable->getMessage();

            Log::critical('Error during checkout for Job posting subscription', $errorMessage);
            $message = '<@Sydney> *Error during checkout for Job Posting subscription*' . "\n" .
                ">Checkout ID: " . $checkoutId;

            SlackAlert::to('jobs')->message($message);

            return redirect()->route('public.content.jobs.index')->with('error', $errorMessage);
        }
    }
}
