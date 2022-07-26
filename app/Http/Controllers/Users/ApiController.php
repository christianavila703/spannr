<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function createKey(Request $request): \Illuminate\Http\RedirectResponse
    {
        $token = Auth::user()->createToken($request->input('token_name') ?? 'default', [
            'view-organizations'
        ]);

        return redirect()->route('users.settings')->with([
            'apiKeyResponse' => 'Your API key was created! Copy it somewhere safe because it will not be shown again.',
            'apiToken' => $token->plainTextToken
        ]);
    }

    public function deleteKey(Request $request): \Illuminate\Http\RedirectResponse
    {
        Auth::user()->tokens()->where('id', $request->input('token_id'))->delete();

        return redirect()->route('users.settings')->with([
            'apiKeyResponse' => 'Your API key was deleted'
        ]);
    }
}
