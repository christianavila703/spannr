<?php

namespace App\Http\Livewire;

use App\Models\Activity;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLog extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;
    public $logName;
    public $perPage = 25;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLogName()
    {
        $this->resetPage();
    }

    public function resetFilters() {
        $this->search = '';
        $this->logName = '';
    }

    public function render()
    {
        return view('livewire.activity-log', [
            'activities' => Activity::orderByDesc('created_at')
                ->when($this->logName, function($query, $logName) {
                      return $query->where('log_name', $logName);
                })
                ->when($this->search, function($query, $search) {
                    return $query
                        ->where('log_name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                })
                ->paginate($this->perPage)
        ]);
    }
}
