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

        if ($user->isSubscriptionActive() === false) {
            return view('panel.signup');
        }

        return view('panel.index', [
            'subscription' => $subscription
        ]);
    }
}
