<?php

namespace App\Http\Livewire\Auth;

use App\Http\Livewire\Public\Marketplace\IntakeForm;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Throwable;

class Register extends Component
{
    public $name;
    public $last_name;
    public $email;
    public $password;

    public ?string $success = null;
    public ?string $error = null;

    // TODO: Need to pass param to send the event to
    // TODO: Create or connect Person

    public function rules() {
        return [
            'name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|max:200'
        ];
    }

    public function submit() {
        $this->validate();

        try {
            User::create([
                'name' => $this->name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            if (Auth::attempt(['email' => $this->email, 'password' => $this->password], true)) {
                $this->success = 'Success! You are logged in.';
                $this->emitTo(IntakeForm::class, 'userLoggedIn');
            } else {
                $this->error = 'There was a problem authenticating you. Please contact sydney@spannr.com.';
            }

        } catch(Throwable $exception) {
            // TODO: Log this & notify
            $this->error = "Sorry, we're having trouble registering you. Please try again later or contact help@spannr.com.";
        }
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
