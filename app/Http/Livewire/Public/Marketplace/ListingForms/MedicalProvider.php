<?php

namespace App\Http\Livewire\Public\Marketplace\ListingForms;

use App\Models\Listing;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MedicalProvider extends Component
{
    protected $listeners = ['updateDescription'];

    public Listing $listing;
    public bool $canEditUsers = false;

    protected $rules = [
        'listing.phone' => 'nullable',
        'listing.website' => 'required|url',
        'listing.summary' => 'required|string|max:250',
        'listing.insurance' => 'nullable|boolean',
        'listing.virtual' => 'nullable|boolean',
        'listing.description' => 'nullable'
    ];

    protected $messages = [
        'listing.virtual.required' => 'Please select an option.',
        'listing.insurance.required' => 'Please select an option.'
    ];

    public function submit() {
        $this->validate();
        $this->listing->save();
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Listing updated!']);
    }

    public function updateDescription($content) {
        $this->listing->description = $content;
    }

    public function loadListingEditor() {
        $this->dispatchBrowserEvent('load-listing-editor');
    }

    public function render()
    {
        return view('livewire.public.marketplace.listing-forms.medical-provider');
    }
}
