<div>
    <div class="mb-4 d-flex flex-wrap">
        <div class="me-4">
            <input wire:model="filters.search" type="text" class="form-control" placeholder="Search...">
        </div>
        <div class="me-4 my-2">
            <input wire:model="filters.show-current-events" class="form-check-input" type="checkbox" id="filter-show-current-events" @if($filters['show-current-events']) checked @endif>
            <label class="form-check-label me-3" for="filter-show-current-events">
                Current Events
            </label>
            <input wire:model="filters.show-past-events" class="form-check-input" type="checkbox" id="filter-show-past-events" @if($filters['show-past-events']) checked @endif>
            <label class="form-check-label" for="filter-show-past-events">
                Past Events
            </label>
        </div>
        <div class="me-4 d-flex">
            <div>
                <input wire:model="filters.date-min" type="date" class="form-control" id="filter-date-min" placeholder="MM/DD/YYYY">
            </div>
            <div>
                <label for="filter-date-min" class="d-block fw-bold m-2 flex-shrink-0">to</label>
                <span class="d-block">&nbsp;</span>
            </div>
            <div>
                <input wire:model="filters.date-max" type="date" class="form-control" id="filter-date-max" placeholder="MM/DD/YYYY">
            </div>
        </div>
        <div class="my-1">
            <button wire:click="resetFilters" class="btn btn-sm">Reset Filters</button>
        </div>
        <div class="ms-auto lead">
            {{ $events->total() }} results
        </div>
    </div>

    <div class="row">
        @foreach($events as $event)
            <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-4 d-flex align-items-stretch">
                <div class="card">
                    @if ($event->image_url)
                        <a href="{{ route('events.show', $event->slug) }}">
                            <img src="{{ $event->image_url }}" alt="{{ $event->name }}" class="card-img-top">
                        </a>
                    @endif
                    <div class="card-body">
                        <p class="card-heading-link mb-0">
                            <a href="{{ route('events.show', $event->slug) }}">{{ $event->name }}</a>
                        </p>
                        <div class="dates fw-bold {{ ($event->status == \App\Models\Event::STATUS_CURRENT) ? 'text-tertiary' : 'text-dark' }} mb-1">
                            @include('admin.events.fields.dates')
                        </div>
                        <x-entities.show.relationship-list :records="$event->eventTypes" class="mb-1" />
                        <div class="description text-muted">
                            {{ $event->summary }}
                        </div>
                        @if($event->remote)
                            <p class="mt-2 mb-0 text-quaternary fw-bold">Remote / Virtual</p>
                        @endif
                        @if($event->locations->count() > 0)
                            <x-entities.show.relationship-list :records="$event->locations" class="mt-1 text-quaternary" />
                        @endif
                    </div>
                    @can('edit records')
                        <div class="card-footer">
                            <div class="edit-link">
                                <a href="{{ route('events.edit', $event->id) }}">Edit</a>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-between my-4">
        <div class="d-flex align-items-center me-4">
            <select wire:model="perPage" id="perPage" class="form-select">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <label for="perPage" class="d-block fw-bold ms-2 flex-shrink-0">Per Page</label>
        </div>

        {{ $events->links() }}
    </div>
</div>
