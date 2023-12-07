<?php

namespace App\Http\Livewire\Public\Marketplace\ManageListing;

use App\Models\Listing;
use Livewire\Component;

class Certifications extends Component
{
    public Listing $listing;
    public array $current = [];
    public $certification;

    public function mount() {
        $this->getCurrentOptions();
    }

    private function getCurrentOptions() {
        $this->listing->refresh();
        $this->current = $this->listing->certifications ?? [];
    }

    public function rules() {
        return [
            'certification' => 'required|string|max:250'
        ];
    }

    public function submit() {
        $this->validate();
        $certifications = $this->listing->certifications;
        $certifications[] = $this->certification;
        $this->listing->certifications = $certifications;
        $this->listing->save();
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Added ' . $this->certification, 'background' => 'bg-success']);
        $this->reset('certification');
        $this->getCurrentOptions();
    }

    public function removeOption($option) {
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Removed ' . $this->current[$option], 'background' => 'bg-danger']);
        unset($this->current[$option]);
        $this->listing->certifications = $this->current;
        $this->listing->save();
    }

    public function render()
    {
        return view('livewire.public.marketplace.manage-listing.certifications');
    }
}
