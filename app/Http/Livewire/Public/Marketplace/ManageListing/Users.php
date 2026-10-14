<?php

namespace App\Http\Livewire\Public\Marketplace\ManageListing;

use App\Mail\Marketplace\MarketplaceNewUserNotification;
use App\Models\Listing;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Throwable;

class Users extends Component
{
    public Listing $listing;

    public $email;
    public $role;
    public bool $send_email = false;

    public bool $showRegistrationForm = false;

    public ?string $first_name = null;
    public ?string $last_name = null;

    public ?string $success = null;
    public ?string $error = null;

    public function rules()
    {
        return [
            'email' => 'required|email',
            'role' => 'required',
            'send_email' => 'boolean|nullable'
        ];
    }

    public function submit() {
        $this->reset('error');
        $this->reset('success');
        $this->reset('showRegistrationForm');

        $this->validate();

        $user = User::where('email', $this->email)->first();

        if ($user) {
            $this->listing->users()->attach($user->id, ['role' => $this->role]);
            $user->assignRole(Role::ROLE_MARKETPLACE_MANAGER);

            $this->listing->refresh();
            $this->success = 'Successfully added ' . $user->name . '!';

            if ($this->send_email) {
                Mail::to($this->email)->send(new MarketplaceNewUserNotification($this->listing, $user, false));
            }

            $this->reset('email');
            $this->reset('role');
            $this->reset('error');
            $this->reset('send_email');
            $this->reset('showRegistrationForm');
            $this->reset('first_name');
            $this->reset('last_name');

        } else {
            $this->reset('success');
            $this->showRegistrationForm = true;

            if ($this->first_name AND $this->last_name) {

                try {
                    $user = User::create([
                        'name' => $this->first_name,
                        'last_name' => $this->last_name,
                        'email' => $this->email,
                        'password' => Hash::make(Str::random()),
                    ]);

                    Mail::to($this->email)->send(new MarketplaceNewUserNotification($this->listing, $user, true));

                } catch(Throwable $exception) {
                    SlackAlert::to('test')->message('Exception during Marketplace Manage Users' . "\n" . $exception->getMessage() . "\n" . $this->email . "\n" . $this->listing->show_url);
                }

                $this->listing->users()->attach($user->id, ['role' => $this->role]);
                $this->listing->refresh();
                $this->success = 'Successfully added ' . $user->name . '!';

                $this->reset('email');
                $this->reset('role');
                $this->reset('error');
                $this->reset('showRegistrationForm');
                $this->reset('send_email');
                $this->reset('first_name');
                $this->reset('last_name');

            } else {
                $this->error = 'That email is not registered, please fill out their name below';
            }
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
