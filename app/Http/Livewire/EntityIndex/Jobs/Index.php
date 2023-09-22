<?php

namespace App\Http\Livewire\EntityIndex\Jobs;

use App\Http\Livewire\EntityIndex\BaseIndex;
use App\Models\Job;
use App\Models\Investor;
use App\Models\JobCategory;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Tag;
use Carbon\Carbon;

class Index extends BaseIndex
{
    public $sortColumnName = 'posted_date';
    public $sortDirection = 'desc';

    public $filters = [
        'search' => '',
        'date-min' => null,
        'date-max' => null,
        'salary-min' => null,
        'salary-max' => null,
        'remote' => null,
        'types' => [],
        'statuses' => [Job::STATUS_OPEN],
        'categories' => [],
        'organizations' => [],
        'investors' => [],
        'locations' => [],
        'tags' => [],
    ];

    public $sortOptions = [
      'name:asc' => 'Name A > Z',
      'name:desc' => 'Name Z > A',
      'posted_date:asc' => 'Oldest > Newest',
      'posted_date:desc' => 'Newest > Oldest',
      'salary_min:asc' => 'Salary Min > Max',
      'salary_max:desc' => 'Salary Max > Min',
    ];

    public function render()
    {
        $typeOptions = Job::TYPES;
        $statusOptions = Job::STATUSES;
        $categories = JobCategory::whereHas('jobs')->get();
        $organizations = Organization::whereHas('jobs')->get();
        $investors = Investor::whereHas('jobs')->get();
        $locations = Location::whereHas('jobs')->get();
        $tags = Tag::whereHas('jobs')->get();

        $records = Job::query()
            ->with(['category'])
            ->when($this->filters['search'], function($query, $search) {
                return $query->where(function ($subquery) use ($search) {
                    $subquery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->when($this->filters['remote'], function($query) {
                return $query->remote();
            })
            ->when($this->filters['types'], function($query, $types) {
                return $query->whereIn('type', $types);
            })
            ->when($this->filters['statuses'], function($query, $statuses) {
                return $query->whereIn('status', $statuses);
            })
            ->when($this->filters['date-min'], fn($query, $date) => $query->where('posted_date', '>=', Carbon::parse($date)))
            ->when($this->filters['date-max'], fn($query, $date) => $query->where('posted_date', '<=', Carbon::parse($date)))
            ->when($this->filters['salary-min'], fn($query, $value) => $query->where('salary_min', '>=', $value))
            ->when($this->filters['salary-max'], fn($query, $value) => $query->where('salary_max', '<=', $value))
            ->when($this->filters['categories'], function($query, $ids) {
                return $query->whereIn('category_id', $ids);
            })
            ->when($this->filters['investors'], function($query, $ids) {
                return $query->where('owner_type', Investor::class)->whereIn('owner_id', $ids);
            })
            ->when($this->filters['organizations'], function($query, $ids) {
                return $query->where('owner_type', Organization::class)->whereIn('owner_id', $ids);
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

        return view('livewire.entity-index.jobs.index', [
            'currentSortOption' => $this->getCurrentSortOption(),
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'categories' => $categories,
            'organizations' => $organizations,
            'investors' => $investors,
            'locations' => $locations,
            'tags' => $tags,
            'records' => $records,
        ]);
    }
}
