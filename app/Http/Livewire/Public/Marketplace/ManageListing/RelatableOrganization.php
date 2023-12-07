<?php

namespace App\Http\Livewire\Public\Marketplace\ManageListing;

use App\Models\Listing;
use App\Models\Organization;
use Livewire\Component;

class RelatableOrganization extends Component
{
    public Listing $listing;
    public $search;
    public array $organizations = [];

    public function updatedSearch() {
        $this->search();
    }

    public function search() {
        $this->organizations = Organization::where('name', 'like', '%' . $this->search . '%')->take(10)->get()->toArray();
    }

    public function setRelatable($relatableId) {
        $this->listing->relatable_type = Organization::class;
        $this->listing->relatable_id = $relatableId;
        $this->listing->save();
        $this->listing->refresh();
    }

    public function resetRelatable() {
        $this->listing->relatable_type = NULL;
        $this->listing->relatable_id = NULL;
        $this->listing->save();
        $this->listing->refresh();
        $this->search = $this->listing->name;
    }

    public function mount() {
        $this->search = $this->listing->name;
        $this->search();
    }

    public function render()
    {
        return view('livewire.public.marketplace.manage-listing.relatable-organization');
    }
}
