<?php

namespace App\Http\Livewire\Admin\Investors;

use App\Models\Investor;
use Livewire\Component;

class People extends Component
{
    public Investor $investor;
    public int $detachableId;

    public function setDetachablePerson($id) {
        $this->detachableId = $id;
    }

    public function detachPerson() {
        $this->investor->people()->detach($this->detachableId);
    }

    public function render()
    {
        return view('livewire.admin.investors.people', [
            'people' => $this->investor->people
        ]);
    }
}
