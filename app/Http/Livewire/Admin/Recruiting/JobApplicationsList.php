<?php

namespace App\Http\Livewire\Admin\Recruiting;

use App\Http\Livewire\DataTable\WithCachedRows;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Http\Livewire\DataTable\WithSorting;
use App\Models\JobApplication;
use Livewire\Component;

class JobApplicationsList extends Component
{
    use WithPerPagePagination, WithCachedRows, WithSorting;

    protected string $paginationTheme = 'bootstrap';
    public $queryString = ['search', 'app', 'job', 'applicant'];

    public $search;
    public $app;
    public $job;
    public $applicant;

    public $selectedPerson;
    public $selectedJobApplication;

    public $filters = [
        'status' => [],
        'recruiter' => ''
    ];

    public function resetSearch() {
        $this->reset('search');
        $this->resetPage();
    }

    public function UpdatingFilters() {
        $this->resetPage();
    }

    public function updatingSearch() {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset('search');
        $this->reset('app');
        $this->reset('job');
        $this->reset('applicant');
        $this->reset('filters');
        $this->resetPage();
    }

    public function setJobApplication($jobAppId) {
        $jobApp = JobApplication::with('person')->find($jobAppId);
        $this->selectedJobApplication = $jobApp;
        $this->selectedPerson = $jobApp->person;
    }

    public function editJobApplication($jobAppId) {
        $jobApp = JobApplication::with('person')->find($jobAppId);
        $this->selectedJobApplication = $jobApp;
        $this->emit('updateSelectedJobApp', $jobAppId);
    }

    public function getRowsQueryProperty()
    {
        $query = JobApplication::orderByDesc('created_at')
                ->with(['person', 'location', 'job', 'job.owner'])
                ->when($this->app, fn($query, $id) => $query->where('id', $id))
                ->when($this->job, fn($query, $id) => $query->whereHas('job', function($query) use ($id) {
                    $query->where('id', $id);
                }))
                ->when($this->applicant, fn($query, $id) => $query->whereHas('person', function($query) use ($id) {
                    $query->where('id', $id);
                }))
                ->when($this->search, function($query, $search) {
                    return $query
                        ->whereHas('person', function($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%')
                                ->orWhere('phone', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('job', function($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('location', function($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('job.owner', function($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->when($this->filters['status'], fn($query, $status) => $query->whereIn('status', $status))
                ->when($this->filters['recruiter'], fn($query) => $query->where('recruiter', true));

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
        return view('livewire.admin.recruiting.job-applications-list', [
            'records' => $this->rows,
            'statusOptions' => JobApplication::STATUSES
        ]);
    }
}
