<?php

namespace App\Http\Livewire\Public\Marketplace\ManageListing;

use App\Models\Listing;
use App\Models\User;
use Livewire\Component;

class Users extends Component
{
    public Listing $listing;

    public $email;
    public $role;

    public ?string $success = null;
    public ?string $error = null;

    public function rules()
    {
        return [
            'email' => 'required|email',
            'role' => 'required'
        ];
    }

    public function submit() {
        $this->validate();

        $user = User::where('email', $this->email)->first();

        if ($user) {
            $this->listing->users()->attach($user->id, ['role' => $this->role]);
            $this->reset('email');
            $this->reset('role');
            $this->reset('error');

            $this->listing->refresh();
            $this->success = 'Successfully added ' . $user->name . '!';
        } else {
            $this->reset('success');
            $this->error = 'No user found with that email...';
        }
    }

    public function removeUser($userId) {
        $user = User::findOrFail($userId);
        $this->listing->users()->detach($userId);
        $this->listing->refresh();
        $this->success = 'Successfully removed ' . $user->name . '!';
    }

    public function render()
    {
        return view('livewire.public.marketplace.manage-listing.users', [
            'roles' => [Listing::USER_ROLE_MANAGER, Listing::USER_ROLE_OWNER]
        ]);
    }
}
