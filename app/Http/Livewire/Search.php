<?php

namespace App\Http\Livewire;

use App\Models\Organization;
use App\Search\Entities;
use Livewire\Component;

class Search extends Component
{
    public string $query = '';
    public $searchResults;

    public function render()
    {
        $this->searchResults = Entities::search($this->query)->raw();
//        $this->searchResults = Organization::search($this->query)->get();

        return view('livewire.search');
    }
}
