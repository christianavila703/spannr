<?php

namespace App\Http\Livewire\Admin\Locations;

use App\Models\Location;
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
        'has-relations' => [],
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
        $relationNames = ['people', 'investors', 'organizations', 'jobs'];

        return view('livewire.admin.locations.index', [
            'relationNames' => $relationNames,
            'locations' => Location::with($relationNames)
                ->withCount($relationNames)
                ->when($this->filters['search'], function($query, $search) {
                    return $query->where('name', 'like', '%' . $search . '%');
                })
                ->when($this->filters['has-relations'], function($query, $relations) {
                    foreach ($relations as $relation) {
                        $query->whereHas($relation);
                    }

                    return $query;
                })
                ->orderBy($this->sortColumnName, $this->sortDirection)
                ->paginate($this->perPage),
        ]);
    }
}
