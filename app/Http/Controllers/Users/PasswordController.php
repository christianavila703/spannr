<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserChangePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function store(UserChangePasswordRequest $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $currentPassword = $request->input('currentPassword');

        if (!$this->validateUser($currentPassword, $user->password)) {
            return redirect()->route('users.settings')->with('error', 'Wrong current password. If you don\'t remember yours, you can reset your password.');
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();
        return redirect()->route('users.settings')->with('success', 'Your password was successfully updated.');

    }

    private function validateUser($newPassword, $oldPassword) {
        return Hash::check($newPassword, $oldPassword);
    }
}
