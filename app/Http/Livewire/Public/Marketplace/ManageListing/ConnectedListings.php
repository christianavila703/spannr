<?php

namespace App\Http\Livewire\Public\Marketplace\ManageListing;

use App\Models\Listing;
use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ConnectedListings extends Component
{
    public Listing $listing;
    public $search;
    public array $connectedListings = [];

    public $connectedListing;
    public $label;

    public function updatedSearch() {
        $this->search();
    }

    public function rules() {
        return [
            'label' => 'required'
        ];
    }

    public function search() {
        $this->connectedListings = Listing::public()->where('name', 'like', '%' . $this->search . '%')
            ->take(10)->get()->toArray();
    }

    public function setRelatable($relatableId) {
        $listing = Listing::findOrFail($relatableId);
        $this->connectedListing = $listing;
    }

    public function submit() {
        $this->validate();
        $this->listing->connectedListings()->attach($this->connectedListing->id, ['label' => $this->label]);
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Added ' . $this->connectedListing->name, 'background' => 'bg-success']);
        $this->reset('search');
        $this->reset('connectedListings');
        $this->reset('connectedListing');
        $this->reset('label');
    }

    public function removeRelatable($relatableId, $relatableName) {
        $this->listing->connectedListings()->detach($relatableId);
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Removed ' . $relatableName, 'background' => 'bg-danger']);
    }

    public function render()
    {
        return view('livewire.public.marketplace.manage-listing.connected-listings');
    }
}
