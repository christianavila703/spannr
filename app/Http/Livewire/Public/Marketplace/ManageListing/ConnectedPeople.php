<?php

namespace App\Http\Livewire\Public\Marketplace\ManageListing;

use App\Models\Listing;
use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ConnectedPeople extends Component
{
    public Listing $listing;
    public $search;
    public array $people = [];

    public $person;
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
        $this->people = Person::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', $this->search)
            ->orWhereHas('user', function(Builder $query) {
                $query->where('email', $this->search);
            })
            ->take(10)->get()->toArray();
    }

    public function setRelatable($relatableId) {
        $person = Person::findOrFail($relatableId);
        $this->person = $person;
    }

    public function submit() {
        $this->validate();
        $this->listing->people()->attach($this->person->id, ['label' => $this->label]);
        $this->reset('search');
        $this->reset('people');
        $this->reset('person');
        $this->reset('label');
    }

    public function removeRelatable($relatableId) {
        $this->listing->people()->detach($relatableId);
    }

    public function render()
    {
        return view('livewire.public.marketplace.manage-listing.connected-people');
    }
}
