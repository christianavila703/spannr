<?php

namespace App\Http\Livewire\Events;

use App\Models\Event;
use App\Models\EventType;
use App\Models\Job;
use Carbon\Carbon;
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
        'show-past-events' => '',
        'show-current-events' => 1,
        'selected-types' => [],
        'date-min' => null,
        'date-max' => null,
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
        return view('livewire.events.index', [
            'eventTypes' => EventType::all(),
            'statusOptions' => [Event::STATUS_CURRENT, Event::STATUS_PAST],
            'events' => Event::orderByDesc('start_date')
                ->when($this->filters['search'], function($query, $search) {
                    return $query
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                })
                ->when(($this->filters['show-current-events']) AND (!$this->filters['show-past-events']), function($query) {
                    return $query->current();
                })
                ->when(($this->filters['show-past-events']) AND (!$this->filters['show-current-events']), function($query) {
                    return $query->past();
                })
                ->when($this->filters['date-min'], fn($query, $date) => $query->where('start_date', '>=', Carbon::parse($date)))
                ->when($this->filters['date-max'], fn($query, $date) => $query->where('start_date', '<=', Carbon::parse($date)))
                ->paginate($this->perPage)
        ]);
    }
}
