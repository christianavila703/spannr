<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $user = User::with(['jobSubscriptions'])
            ->withCount(['jobSubscriptions', 'jobs'])
            ->where('id', auth()->id())->firstOrFail();

        $apiKeys = $user->tokens;

        return view('users.dashboard', [
            'user' => $user,
            'apiKeys' => $apiKeys
        ]);
    }
}
