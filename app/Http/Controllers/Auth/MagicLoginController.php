<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MagicLinkToken;
use App\Models\User;
use App\Notifications\SendLoginLink;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MagicLoginController extends Controller
{

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login-with-link');
    }

    public function sendLoginLink(Request $request)
    {
        $email = $request->input('email');

        $user = User::where('email', $email)->first();

        if ($user === null) {
            return redirect()->route('login.link')->with('error', 'User with email ' . $email . ' not found!');
        }

        $magicLinkToken = MagicLinkToken::updateOrCreate([
            'user_id' => $user->getKey()
        ], [
            'token' => hash('sha256', Str::random(32)),
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        $user->notifyNow(new SendLoginLink($magicLinkToken));

        return redirect()->route('login.link')->with('success', 'Your login link was sent to ' . $email);
    }

    public function login($token)
    {
        $magicToken = MagicLinkToken::with('user')->where('token', $token)->first();

        if ($magicToken === null) {
            return redirect($this->redirectTo);
        }

        if (Carbon::now() > $magicToken->expires_at) {
            return redirect()->route('login.link')->with('error', 'Login link is outdated. Please make a new request.');
        }

        Auth::login($magicToken->user);
        $magicToken->delete();

        return redirect($this->redirectTo);

    }
}
