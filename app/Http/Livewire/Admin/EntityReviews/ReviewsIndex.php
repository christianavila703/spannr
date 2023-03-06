<?php

namespace App\Http\Livewire\Admin\EntityReviews;

use App\Models\EntityReviewNote;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewsIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    /** @var string */
    public $entityClass;

    public $entityRouteNames;

    public $perPage = 25;

    public $filters = [
        'search' => '',
        'priorities' => [],
    ];

    public array $priorities = [];

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
        $userId = auth()->user()->id;
        $requiredReviewers = config('entity-reviews.required_reviewers');
        $entityClass = $this->entityClass;
        $entityTable = (new $entityClass)->getTable();
        $entityReviewNotesTable = (new EntityReviewNote)->getTable();

        $items = $this->entityClass::query()
            ->unreviewedByUserId($userId)
            ->with('reviewNote')
            ->when($this->filters['search'], function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->when($this->filters['priorities'],
                function ($query, $priorities) use ($entityClass, $entityTable, $entityReviewNotesTable) {
                    return $query->join($entityReviewNotesTable, $entityReviewNotesTable . '.entity_id', '=', $entityTable . '.id')
                        ->where($entityReviewNotesTable . '.entity_type', '=', $entityClass)
                        ->whereIn($entityReviewNotesTable . '.priority', $priorities);
                })
            ->orderBy($this->sortColumnName, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.entity-reviews.reviews-index', [
            'items' => $items,
            'requiredReviewers' => $requiredReviewers
        ]);
    }
}
