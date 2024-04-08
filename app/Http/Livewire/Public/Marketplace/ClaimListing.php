<?php

namespace App\Http\Livewire\Public\Marketplace;

use App\Models\Listing;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Throwable;

class ClaimListing extends Component
{
    public Listing $listing;

    public bool $allowClaim = false;
    public bool $allowAutomaticClaim = false;
    public bool $showForm = false;

    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $email = null;

    public ?string $listingDomain = null;

    public bool $success = false;
    public bool $manualClaimSuccess = false;

    public bool $error = false;

    public function mount() {
        if ($this->listing->users_count === 0) {
            $this->allowClaim = true;
        }

        if ($this->listing->website) {
            $domain = $this->getDomainFromURL($this->listing->website);

            if ($domain) {
                $this->listingDomain = $domain;
                $this->allowAutomaticClaim = true;
            }
        }
    }

    public function showClaimForm() {
        $this->showForm = true;
    }

    public function rules() {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email'
        ];
    }

    public function save() {
        $this->validate();

        $emailParts = explode('@', $this->email);
        $emailDomain = $emailParts[1];

        if($emailDomain === $this->listingDomain) {

            try {

                $user = User::firstOrCreate(
                    ['email' => $this->email],
                    [
                        'name' => $this->first_name,
                        'last_name' => $this->last_name,
                        'email' => $this->email,
                        'password' => Hash::make(Str::random()),
                    ]
                );

                $this->listing->users()->attach($user->id, ['role' => Listing::USER_ROLE_OWNER]);
                $user->assignRole(Role::ROLE_MARKETPLACE_MANAGER);

                $this->success = true;

            } catch (Throwable $exception) {
                SlackAlert::to('marketplace')->message('<@sydney> Exception during Claim Listing with successful domain match for #' . $this->listing->id . ' ' .
                    $this->listing->name  . "\n" . $exception->getMessage() . "\n" . $this->email);

                $this->error = true;
            }

        } else {

            try {
                $user = User::firstOrCreate(
                    ['email' => $this->email],
                    [
                        'name' => $this->first_name,
                        'last_name' => $this->last_name,
                        'email' => $this->email,
                        'password' => Hash::make(Str::random()),
                    ]
                );

                $user->assignRole(Role::ROLE_MARKETPLACE_MANAGER);

                SlackAlert::to('marketplace')->message('<@sydney> Manual Marketplace Listing Claim Verification' .  "\n" .
                    ' #' . $this->listing->id . ' ' . $this->listing->name  . "\n" .
                    'Name: ' . $this->first_name . ' ' . $this->last_name . "\n" .
                    'User ID: ' . $user->id . "\n" .
                    'Email: ' . $this->email . "\n" .
                    $this->listing->show_url
                );

                $this->manualClaimSuccess = true;

            } catch(Throwable $exception) {
                SlackAlert::to('marketplace')->message('<@sydney> Exception during Claim Listing for manual verification #' . $this->listing->id . ' ' .
                    $this->listing->name  . "\n" . $exception->getMessage() . "\n" . $this->email);

                $this->error = true;
            }
        }
    }

    private function getDomainFromURL($url){
    $pieces = parse_url($url);
        $domain = $pieces['host'] ?? '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }
        return null;
    }

    public function render()
    {
        return view('livewire.public.marketplace.claim-listing');
    }
}
