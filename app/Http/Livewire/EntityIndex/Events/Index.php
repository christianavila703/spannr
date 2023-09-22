<?php

namespace App\Http\Livewire\EntityIndex\Events;

use App\Http\Livewire\EntityIndex\BaseIndex;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Investor;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Person;
use App\Models\Tag;
use Carbon\Carbon;

class Index extends BaseIndex
{
    public $sortColumnName = 'start_date';
    public $sortDirection = 'asc';

    public $filters = [
        'search' => '',
        'show-past' => 0,
        'show-current' => 1,
        'date-min' => null,
        'date-max' => null,
        'remote' => 0,
        'selected-types' => [],
        'has' => [],
        'people' => [],
        'organizations' => [],
        'investors' => [],
        'locations' => [],
        'tags' => [],
    ];

    public $sortOptions = [
      'name:asc' => 'Name A > Z',
      'name:desc' => 'Name Z > A',
      'start_date:asc' => 'Oldest > Newest',
      'start_date:desc' => 'Newest > Oldest',
    ];

    public function render()
    {
        $typeOptions = EventType::whereHas('events')->pluck('name', 'id')->toArray();
        $people = Person::whereHas('events')->get();
        $organizations = Organization::whereHas('events')->get();
        $investors = Investor::whereHas('events')->get();
        $locations = Location::whereHas('events')->get();
        $tags = Tag::whereHas('events')->get();

        $records = Event::query()
            ->when($this->filters['search'], function($query, $search) {
                return $query->where(function ($subquery) use ($search) {
                    $subquery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->when(($this->filters['show-current']) AND (!$this->filters['show-past']), function($query) {
                return $query->current();
            })
            ->when(($this->filters['show-past']) AND (!$this->filters['show-current']), function($query) {
                return $query->past();
            })
            ->when($this->filters['remote'], function($query) {
                return $query->remote();
            })
            ->when($this->filters['date-min'], fn($query, $date) => $query->where('start_date', '>=', Carbon::parse($date)))
            ->when($this->filters['date-max'], fn($query, $date) => $query->where('start_date', '<=', Carbon::parse($date)))
            ->when($this->filters['selected-types'], function($query, $ids) {
                return $query->whereHas('eventTypes', function ($subquery) use ($ids) {
                    $subquery->whereIn('id', $ids);
                });
            })
            ->when($this->filters['has'], function($query, $values) {
                foreach ($values as $value) {
                    $query->has($value);
                }

                return $query;
            })
            ->when($this->filters['people'], function($query, $ids) {
                return $query->whereHas('people', function ($subquery) use ($ids) {
                    $subquery->whereIn('id', $ids);
                });
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

        return view('livewire.entity-index.events.index', [
            'currentSortOption' => $this->getCurrentSortOption(),
            'typeOptions' => $typeOptions,
            'statusOptions' => Event::STATUSES,
            'people' => $people,
            'organizations' => $organizations,
            'investors' => $investors,
            'locations' => $locations,
            'tags' => $tags,
            'records' => $records,
        ]);
    }
}
