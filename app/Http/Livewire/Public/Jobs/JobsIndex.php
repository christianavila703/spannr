<?php

namespace App\Http\Livewire\Public\Jobs;

use App\Http\Livewire\DataTable\WithCachedRows;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Http\Livewire\DataTable\WithSorting;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Location;
use Carbon\Carbon;
use Livewire\Component;

class JobsIndex extends Component
{
    use WithPerPagePagination, WithCachedRows, WithSorting;

    protected string $paginationTheme = 'bootstrap';
    public $queryString = ['search'];

    public $search;

    public $filters = [
        'date-min' => null,
        'date-max' => null,
        'type' => [],
        'category' => [],
        'remote' => null
    ];

    public $jobTypes;
    public $categoryOptions;
    public $locationOptions;

    public function mount()
    {
        $this->jobTypes = Job::select('type')->distinct()->pluck('type')->toArray();
        $this->categoryOptions = JobCategory::has('jobs')->pluck('name', 'id')->toArray();
        $this->locationOptions = $this->customLocations();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset('search');
        $this->reset('filters');
        $this->resetPage();
    }

    public function resetSearch() {
        $this->reset('search');
        $this->resetPage();
    }

    public function resetFilter($filter) {
        $this->filters[$filter] = null;
        $this->resetPage();
    }

    public function removeFilterArrayItem($filterName, $categoryId) {
        $key = array_search($categoryId, $this->filters[$filterName]);

        if ($key !== false) {
            unset($this->filters[$filterName][$key]);
        }

        $this->resetPage();
    }

    public function getRowsQueryProperty()
    {
        $query = Job::orderByDesc('posted_date')->open()
                ->when($this->search, function($query, $search) {
                    return $query
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('type', 'like', '%' . $search . '%')
                        ->orWhereHas('locations', function($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('owner', function($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->when(array_filter($this->filters['category']), function($query, $categories) {
                    return $query->whereHas('category', function($query) use ($categories) {
                         $query->whereIn('name', $categories);
                    });
                })
                ->when(array_filter($this->filters['type']), fn($query, $type) => $query->whereIn('type', $type))
                ->when($this->filters['remote'], fn($query) => $query->where('remote', true));

        return $this->applySorting($query);
    }

    public function getRowsProperty()
    {
        return $this->cache(function () {
            return $this->applyPagination($this->rowsQuery);
        });
    }

    public function render()
    {
        return view('livewire.public.jobs.jobs-index', [
            'records' => $this->rows,
            'typeOptions' => $this->jobTypes,
            'locationOptions' => $this->locationOptions,
            'categoryOptions' => $this->categoryOptions,
        ]);
    }

    private function customLocations(): array
    {
        $allLocations = Location::has('jobs')->get();

        $locationsArray = [];
        $usaArray = [];

        foreach ($allLocations as $location) {
            $parts = explode(',', $location->name);
            $reversed = array_reverse($parts);
            $country = trim($reversed[0]);

            if ($country === 'USA') {
                if (array_key_exists(1, $parts)) {
                    $usaArray[] = $parts[1];
                }
            }

            $locationsArray[] = $country;
        }

        $newArray = array_merge($locationsArray, $usaArray);

        return array_unique($newArray);
    }
}
