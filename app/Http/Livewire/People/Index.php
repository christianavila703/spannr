<?php

namespace App\Http\Livewire\People;

use App\Models\Person;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;
    public $perPage = 25;

    public $filters = [
        'search' => '',
        'owned-investor' => null
    ];

    public $sortColumnName = 'name';
    public $sortDirection = 'asc';

    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function resetFilters() {
        $this->reset('filters');
    }

    public function sortBy($columnName)
    {
        if ($this->sortColumnName === $columnName) {
            $this->sortDirection = ($this->sortDirection === 'asc' ? 'desc' : 'asc');
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortColumnName = $columnName;
    }

    public function render()
    {
        return view('livewire.people.index', [
            'people' => Person::when($this->filters['search'], function($query, $search) {
                    return $query
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('byline', 'like', '%' . $search . '%');
                })
                ->when($this->filters['owned-investor'], function($query) {
                    return $query->has('ownedInvestor');
                })
                ->orderBy($this->sortColumnName, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
