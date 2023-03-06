<?php

namespace App\Http\Livewire\Admin\EntityReviews;

use Livewire\Component;
use Livewire\WithPagination;

class AuditsIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    /** @var string */
    public $entityClass;

    public $entityRouteNames;

    public $perPage = 25;

    public $filters = [
        'search' => ''
    ];

    public $sortColumnName = 'name';
    public $sortDirection  = 'asc';

    public function mount($entityClass)
    {
        $this->entityClass = $entityClass;
        $this->entityRouteNames = config('route-mapping.admin.' . $entityClass);
    }

    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
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
        $items = $this->entityClass::query()
            ->auditRequired()
            ->with('reviews')
            ->when($this->filters['search'], function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy($this->sortColumnName, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.entity-reviews.audits-index', ['items' => $items]);
    }
}
