<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserChangePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function index() {
        return view ('users.change-password');
    }

    public function store(UserChangePasswordRequest $request) {
        $user = Auth::user();
        $currentPassword = $request->input('currentPassword');

        if (!$this->validateUser($currentPassword, $user->password)) {
            return redirect()->route('users.change-password')->with('error', 'Wrong current password. If you don\'t remember yours, you can reset your password.');
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();
        return redirect()->route('users.change-password')->with('success', 'Your password was successfully updated.');

    }

    private function validateUser($newPassword, $oldPassword) {
        return Hash::check($newPassword, $oldPassword);
    }
}
