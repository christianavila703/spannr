<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $user = auth()->user();

        if (!$user) {
            return view('panel.signup');
        }

        $subscription = auth()->user()->subscription()->first();
        $subscriptionUrl = null;

        if ($user->isSubscriptionActive() === false) {
            $subscriptionUrl = config('services.stripe.spannr_plus_subscription_url') .
                '?client_reference_id=' . $user->id;
        }

        return view('panel.index', [
            'subscription' => $subscription,
            'subscriptionUrl' => $subscriptionUrl,
        ]);
    }
}
