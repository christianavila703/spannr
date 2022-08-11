<?php

namespace App\Http\Livewire\Admin\Jobs;

use App\Models\Job;
use App\Models\JobCategory;
use Livewire\Component;
use Livewire\WithPagination;

class JobCategoriesIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;
    public $perPage = 25;

    public $filters = [
        'search' => '',
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
        return view('livewire.admin.jobs.job-categories-index', [
            'jobCategories' => JobCategory::orderBy('name')
                ->withCount(['jobs'])
                ->when($this->filters['search'], function($query, $search) {
                    return $query
                        ->where('name', 'like', '%' . $search . '%');
                })
                ->paginate($this->perPage)
        ]);
    }
}
