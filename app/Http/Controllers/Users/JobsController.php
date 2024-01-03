<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\SetupIntent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Stripe\Exception\InvalidRequestException;
use Stripe\StripeClient;

class JobsController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view ('users.jobs.index');
    }

    public function unsubscribe($id): \Illuminate\Http\RedirectResponse
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

    public function updateCard($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $user = auth()->user();
        $job = $user->jobs()->with('jobSubscription')->findOrFail($id);

        return view('users.jobs.update-card', [
            'job' => $job,
            'user' => $user
        ]);
    }

    public function updateCardSetupIntent($subscriptionId, $stripeCustomerId): \Illuminate\Http\JsonResponse
    {
        $user = User::where('stripe_customer_id', $stripeCustomerId)->firstOrFail();
        $job = Job::where('subscription_id', $subscriptionId)->firstOrFail();

        $setupIntent = $job->setupIntents()->whereIn('status', SetupIntent::STATUSES_PROCEED_WITH_SETUP_INTENT)->first();

        if ($setupIntent) {
            return response()->json([
                'clientSecret' => $setupIntent->data['client_secret'],
            ]);
        }

        try {
            $stripe = new StripeClient(config("services.stripe.api_key"));

            $setupIntent = $stripe->setupIntents->create([
                'customer' => $user->stripe_customer_id,
                'automatic_payment_methods' => ['enabled' => true],
                'usage' => 'off_session',
                'metadata' => [
                    'job_id' => $job->id,
                    'subscription_id' => $job->subscription_id,
                    'user_id' => $user->id,
                    'subscription_type' => SetupIntent::TYPE_JOB_SUBSCRIPTION
                ]
            ]);

            return response()->json([
                'clientSecret' => $setupIntent->client_secret,
            ]);

        } catch(InvalidRequestException $exception) {
            SlackAlert::to('test')->message("<@sydney> Exception during Update Monthly Job Card for Job " . $job->id . ' ' . $exception->getMessage());
        }

        return response()->json([
            'clientSecret' => NULL,
        ]);
    }

    public function updateCardConfirmation(Request $request, $id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $setupIntent = SetupIntent::findOrFail($request->input('setup_intent'));

        $user = auth()->user();
        $job = $user->jobs()->findOrFail($id);

        return view('users.jobs.update-card-confirmation', [
            'job' => $job,
            'setupIntent' => $setupIntent
        ]);
    }
}
