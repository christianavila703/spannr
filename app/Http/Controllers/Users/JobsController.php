<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Stripe\StripeClient;

class JobsController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view ('users.jobs');
    }

    public function unsubscribe($id)
    {
        $user = auth()->user();
        $job = $user->jobs()->findOrFail($id);

        try {
            $stripe = new StripeClient(config('services.stripe.api_key'));
            $response = $stripe->subscriptions->cancel($job->subscription_id);

            if ($response->status != 'canceled') {
                throw new \Exception('Unable to cancel subscription for job posting.');
            }

            $job->monthly = null;
            $job->subscription_id = null;
            $job->save();

        } catch (\Throwable $throwable) {
            Log::critical('Error during cancellation Job Posting subscription. Job ID: ' . $job->id, [$throwable->getMessage()]);
            $message = '<@Sydney> *Error during cancellation Job Posting subscription*' . "\n" .
                ">Job ID: " . $job->id . "\n" .
                ">Subscription ID: " . $job->subscription_id;

            SlackAlert::to('jobs')->message($message);

              return redirect()->back()->with('error', 'Error when unsubscribing from a job posting. The report has been sent to our team. Please contact us for more details.');
        }

        return redirect()->back()->with('success', 'Your Job Posting subscription was cancelled.');
    }
}
