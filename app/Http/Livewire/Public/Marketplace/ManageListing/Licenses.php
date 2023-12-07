<?php

namespace App\Http\Livewire\Public\Marketplace\ManageListing;

use App\Models\Listing;
use Livewire\Component;

class Licenses extends Component
{
    public Listing $listing;
    public array $current = [];
    public $license;

    public function mount() {
        $this->getCurrentOptions();
    }

    private function getCurrentOptions() {
        $this->listing->refresh();
        $this->current = $this->listing->licenses ?? [];
    }

    public function rules() {
        return [
            'license' => 'required|string'
        ];
    }

    public function submit() {
        $this->validate();
        $licenses = $this->listing->licenses;
        $licenses[] = $this->license;
        $this->listing->licenses = $licenses;
        $this->listing->save();
        $this->reset('license');
        $this->getCurrentOptions();
    }

    public function removeOption($option) {
        unset($this->current[$option]);
        $this->listing->licenses = $this->current;
        $this->listing->save();
    }

    public function render()
    {
        return view('livewire.public.marketplace.manage-listing.licenses');
    }
}
