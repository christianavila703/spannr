<?php

namespace App\Http\Livewire\Admin\Organizations;

use App\Models\Organization;
use Livewire\Component;

class People extends Component
{
    public Organization $organization;
    public int $detachableId;

    public function setDetachablePerson($id) {
        $this->detachableId = $id;
    }

    public function detachPerson() {
        $this->organization->people()->detach($this->detachableId);
    }

    public function render()
    {
        return view('livewire.admin.organizations.people', [
            'people' => $this->organization->people
        ]);
    }
}
