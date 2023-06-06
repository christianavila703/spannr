<?php

namespace App\Http\Livewire\Organizations;

use App\Models\Organization;
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
        'amount-min' => null,
        'amount-max' => null,
        'selected-types' => [],
        'hasStock' => 0,
        'owned-investor' => null,
    ];

    public $sortColumnName = 'name';
    public $sortDirection = 'asc';

    public array $selectedTypes = [];
    public $hasStock;

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
        return view('livewire.organizations.index', [
            'typeOptions' => Organization::pluck('type')->unique()->toArray(),
            'organizations' => Organization::with(['people', 'investors', 'fundraisingRounds', 'stock'])
                ->when($this->filters['search'], function($query, $search) {
                    return $query
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('summary', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhereHas('investors', function($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('people', function($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('fundraisingRounds', function($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->when($this->filters['selected-types'], function($query, $selectedTypes) {
                    return $query->whereIn('type', $selectedTypes);
                })
                ->when($this->filters['hasStock'] == 1, function($query) {
                    return $query->whereHas('stock');
                })
                ->when($this->filters['owned-investor'], function($query) {
                    return $query->has('ownedInvestor');
                })
                ->orderBy($this->sortColumnName, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
