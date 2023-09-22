<?php

namespace App\Http\Livewire\EntityIndex\Locations;

use App\Http\Livewire\EntityIndex\BaseIndex;
use App\Models\Investor;
use App\Models\Job;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Person;

class Index extends BaseIndex
{
    public $filters = [
        'search' => '',
        'has' => [],
        'organizations' => [],
        'investors' => [],
        'people' => [],
        'jobs' => [],
    ];

    public $sortOptions = [
        'name:asc' => 'Name A > Z',
        'name:desc' => 'Name Z > A',
        'organizations_count:asc' => 'Organizations Min > Max',
        'organizations_count:desc' => 'Organizations Max > Min',
        'investors_count:asc' => 'Investors Min > Max',
        'investors_count:desc' => 'Investors Max > Min',
        'people_count:asc' => 'People Min > Max',
        'people_count:desc' => 'People Max > Min',
        'jobs_count:asc' => 'Jobs Min > Max',
        'jobs_count:desc' => 'Jobs Max > Min',
    ];

    public function render()
    {
        $relationNames = ['people', 'investors', 'organizations', 'jobs'];

        $organizations = Organization::whereHas('locations')->get();
        $investors = Investor::whereHas('locations')->get();
        $people = Person::whereHas('locations')->get();
        $jobs = Job::whereHas('locations')->get();

        $records = Location::query()
            ->with($relationNames)
            ->withCount($relationNames)
            ->when($this->filters['search'], function($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->when($this->filters['has'], function($query, $values) {
                foreach ($values as $value) {
                    $query->has($value);
                }

                return $query;
            })
            ->when($this->filters['investors'], function($query, $ids) {
                return $query->whereHas('investors', function ($subquery) use ($ids) {
                    $subquery->whereIn('id', $ids);
                });
            })
            ->when($this->filters['organizations'], function($query, $ids) {
                return $query->whereHas('organizations', function ($subquery) use ($ids) {
                    $subquery->whereIn('id', $ids);
                });
            })
            ->when($this->filters['people'], function($query, $ids) {
                return $query->whereHas('people', function ($subquery) use ($ids) {
                    $subquery->whereIn('id', $ids);
                });
            })
            ->when($this->filters['jobs'], function($query, $ids) {
                return $query->whereHas('jobs', function ($subquery) use ($ids) {
                    $subquery->whereIn('id', $ids);
                });
            })
            ->orderBy($this->sortColumnName, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.entity-index.locations.index', [
            'currentSortOption' => $this->getCurrentSortOption(),
            'relationNames' => $relationNames,
            'organizations' => $organizations,
            'investors' => $investors,
            'people' => $people,
            'jobs' => $jobs,
            'records' => $records,
        ]);
    }
}
