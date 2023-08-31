<?php

namespace App\Http\Livewire\Admin\Investors;

use App\Models\Investor;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Person;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $perPage = 24;

    public $filters = [
        'search' => '',
        'selected-types' => [],
        'with' => [],
        'has' => [],
        'people' => [],
        'organizations' => [],
        'locations' => [],
        'tags' => [],
    ];

    public $sortOptions = [
        'name-asc' => 'Name A > Z',
        'name-desc' => 'Name Z > A',
        'type-asc' => 'Type A > Z',
        'type-desc' => 'Type Z > A',
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

    public function sortBy($value)
    {
        $sortValues = explode('-', $value);
        $this->sortColumnName = isset($sortValues[0]) ? $sortValues[0] : 'name';
        $this->sortDirection = isset($sortValues[1])  ? $sortValues[1] : 'asc';
    }

    public function render()
    {
        $typeOptions = Investor::pluck('type')->unique()->toArray();
        $people = Person::whereHas('investors')->get();
        $organizations = Organization::whereHas('investors')->get();
        $locations = Location::whereHas('investors')->get();
        $tags = Tag::whereHas('investors')->get();

        $records = Investor::with(['organizations', 'people', 'locations', 'tags'])
            ->when($this->filters['search'], function($query, $search) {
                return $query
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('summary', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->when($this->filters['selected-types'], function($query, $selectedTypes) {
                return $query->whereIn('type', $selectedTypes);
            })
            ->when($this->filters['has'], function($query, $values) {
                foreach ($values as $value) {
                    $query->has($value);
                }

                return $query;
            })
            ->when($this->filters['with'], function($query, $values) {
                foreach ($values as $value) {
                    $query->whereNotNull($value);
                }

                return $query;
            })
            ->when($this->filters['people'], function($query, $ids) {
                return $query->whereHas('people', function ($subquery) use ($ids) {
                    $subquery->whereIn('id', $ids);
                });
            })
            ->when($this->filters['organizations'], function($query, $ids) {
                return $query->whereHas('organizations', function ($subquery) use ($ids) {
                    $subquery->whereIn('id', $ids);
                });
            })
            ->when($this->filters['locations'], function($query, $ids) {
                return $query->whereHas('locations', function ($subquery) use ($ids) {
                    $subquery->whereIn('id', $ids);
                });
            })
            ->when($this->filters['tags'], function($query, $ids) {
                return $query->whereHas('tags', function ($subquery) use ($ids) {
                    $subquery->whereIn('id', $ids);
                });
            })
            ->orderBy($this->sortColumnName, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.investors.index', [
            'typeOptions' => $typeOptions,
            'people' => $people,
            'organizations' => $organizations,
            'locations' => $locations,
            'tags' => $tags,
            'records' => $records,
        ]);
    }
}
