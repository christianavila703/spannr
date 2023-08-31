<?php

namespace App\Http\Livewire\Admin\People;

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
        'owned-investor' => null,
        'with' => [],
        'has' => [],
        'organizations' => [],
        'investors' => [],
        'locations' => [],
        'tags' => [],
    ];

    public $sortOptions = [
        'name-asc' => 'Name A > Z',
        'name-desc' => 'Name Z > A',
        'byline-asc' => 'Byline A > Z',
        'byline-desc' => 'Byline Z > A',
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
        $organizations = Organization::whereHas('people')->get();
        $investors = Investor::whereHas('people')->get();
        $locations = Location::whereHas('people')->get();
        $tags = Tag::whereHas('people')->get();

        $records = Person::when($this->filters['search'], function($query, $search) {
                return $query->where(function ($subquery) use ($search) {
                    $subquery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('byline', 'like', '%' . $search . '%');
                });
            })
            ->when($this->filters['owned-investor'], function($query) {
                return $query->has('ownedInvestor');
            })
            ->when($this->filters['with'], function($query, $values) {
                foreach ($values as $value) {
                    $query->whereNotNull($value);
                }

                return $query;
            })
            ->when($this->filters['has'], function($query, $values) {
                foreach ($values as $value) {
                    $query->has($value);
                }

                return $query;
            })
            ->when($this->filters['organizations'], function($query, $ids) {
                return $query->whereHas('organizations', function ($subquery) use ($ids) {
                    $subquery->whereIn('id', $ids);
                });
            })
            ->when($this->filters['investors'], function($query, $ids) {
                return $query->whereHas('investors', function ($subquery) use ($ids) {
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

        return view('livewire.admin.people.index', [
            'organizations' => $organizations,
            'investors' => $investors,
            'locations' => $locations,
            'tags' => $tags,
            'records' => $records,
        ]);
    }
}
