<?php

namespace App\Http\Livewire\Admin\Recruiting;

use App\Http\Livewire\Traits\WithCachedRows;
use App\Http\Livewire\Traits\WithPerPagePagination;
use App\Http\Livewire\Traits\WithSorting;
use App\Models\Job;
use App\Models\JobCategory;
use Livewire\Component;

class JobsList extends Component
{
    use WithPerPagePagination, WithCachedRows, WithSorting;

    protected string $paginationTheme = 'bootstrap';
    protected $listeners = ['showRecruitingJobDetails'];
    public $queryString = ['search'];

    public $search;

    public $filters = [
        'date-min' => null,
        'date-max' => null,
        'type' => [],
        'category' => [],
        'remote' => null,
        'status' => []
    ];

    public $selectedJob;

    public function showRecruitingJobDetails($jobId) {
        $this->selectedJob = Job::find($jobId);
        $this->dispatchBrowserEvent('showRecruitingJobDetailsModal');
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

    public function resetFilterArray($filter, $key) {
        $filterArray = $this->filters[$filter];

        unset($filterArray[$key]);
        $this->filters[$filter] = $filterArray;

        $this->resetPage();
    }

    public function UpdatingFilters() {
        $this->resetPage();
    }

    public function updatingSearch() {
        $this->resetPage();
    }

    public function getRowsQueryProperty()
    {
        $query = Job::orderByDesc('posted_date')
                ->with(['jobApplications', 'owner'])
                ->where('status', '!=', Job::STATUS_ARCHIVED)
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
                ->when($this->filters['category'], function($query, $categories) {
                    return $query->whereHas('category', function($query) use ($categories) {
                         $query->whereIn('name', $categories);
                    });
                })
                ->when($this->filters['type'], fn($query, $type) => $query->whereIn('type', $type))
                ->when($this->filters['status'], fn($query, $status) => $query->whereIn('status', $status))
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
        return view('livewire.admin.recruiting.jobs-list', [
            'records' => $this->rows,
            'typeOptions' => Job::TYPES,
            'categoryOptions' => JobCategory::has('jobs')->pluck('name', 'id')->toArray(),
            'statusOptions' => [Job::STATUS_OPEN, Job::STATUS_PENDING]
        ]);
    }
}
