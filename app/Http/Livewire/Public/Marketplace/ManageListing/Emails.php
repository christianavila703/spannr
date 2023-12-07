<?php

namespace App\Http\Livewire\Public\Marketplace\ManageListing;

use App\Models\Listing;
use Livewire\Component;

class Emails extends Component
{
    public Listing $listing;

    public $email;

    public function rules() {
        return [
            'email' => 'required|email'
        ];
    }

    public function submit() {
        $this->validate();

        $emails = $this->listing->emails;
        $emails[] = $this->email;
        $emails = array_unique($emails);
        $this->listing->emails = $emails;
        $this->listing->save();
        $this->reset('email');
    }

    public function removeEmail($key) {
        $emails = $this->listing->emails;
        unset($emails[$key]);
        $this->listing->emails = $emails;
        $this->listing->save();
    }

    public function render()
    {
        return view('livewire.public.marketplace.manage-listing.emails');
    }
}
