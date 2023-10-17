<?php

namespace App\Http\Livewire\Public\Plus;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Throwable;

class Signup extends Component
{
    public $email;

    public bool $success = false;
    public bool $error = false;

    public ?string $subscriptionUrl = null;

    public function rules() {
        return [
            'email' => 'required|unique:users,email'
        ];
    }

    public function submit() {
        $this->validate();

        try {
            $userArray = explode('@', $this->email);

            $password = Str::random();
            $user = User::create([
                'name' => $userArray[0],
                'email' => $this->email,
                'password' => Hash::make($password)
            ]);

            $this->subscriptionUrl = config('services.stripe.spannr_plus_subscription_url') . '?client_reference_id=' . $user->id;

            if (Auth::attempt(['email' => $this->email, 'password' => $password], true)) {
                $this->success = true;
            } else {
                $this->error = true;
            }
        } catch(Throwable $exception) {
            // todo: need error
            $this->error = true;
            dd($exception->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.public.plus.signup');
    }
}
