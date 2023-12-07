<?php

namespace App\Http\Livewire\Auth;

use App\Http\Livewire\Public\Marketplace\IntakeForm;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $email;
    public $password;

    public ?string $success = null;
    public ?string $error = null;

    // TODO: Need to pass param to send the event to

    public function rules() {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    public function submit() {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], true)) {
            $this->success = 'Success! You are logged in.';
            $this->emitTo(IntakeForm::class, 'userLoggedIn');
        } else {
            $this->error = 'Your credentials do not match.';
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
