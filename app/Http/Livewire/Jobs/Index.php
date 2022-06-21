<?php

namespace App\Http\Livewire\Jobs;

use App\Models\Job;
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
        'selected-status' => [],
        'selected-types' => [],
    ];

    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function resetFilters() {
        $this->reset('filters');
    }

    public function render()
    {
        return view('livewire.jobs.index', [
            'typeOptions' => Job::pluck('type')->unique()->toArray(),
            'statusOptions' => Job::pluck('status')->unique()->toArray(),
            'jobs' => Job::orderByDesc('posted_date')
                ->with(['owner'])
                ->when($this->filters['search'], function($query, $search) {
                    return $query
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('type', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhereHas('owner', function($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->when($this->filters['selected-types'], function($query, $selectedTypes) {
                    return $query->whereIn('type', $selectedTypes);
                })
                ->when($this->filters['selected-status'], function($query, $selectedStatus) {
                    return $query->whereIn('status', $selectedStatus);
                })
                ->paginate($this->perPage)
        ]);
    }
}
