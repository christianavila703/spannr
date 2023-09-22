<div class="records-index">
    <button class="btn btn-primary d-md-none text-light" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasResponsive" aria-controls="offcanvasResponsive">
        <i class="fa fa-filter"></i>
    </button>

    <div class="row">
        {{--  Filters  --}}
        <div wire:ignore class="records-filters col-3 col-md-4 col-lg-3 col-xl-2">
            <div class="offcanvas-md offcanvas-start" tabindex="-1" id="offcanvasResponsive">
                <div class="offcanvas-header">
                    <h5 class="title">Filters</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                            data-bs-target="#offcanvasResponsive" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="w-100">
                        <div class="mb-4">
                            <input wire:model="filters.search" type="text" class="form-control" placeholder="Search...">
                        </div>

                        <div class="mb-4">
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.remote" class="form-check-input me-2" type="checkbox" id="filters-remote" @if($filters['remote']) checked @endif>
                                <label class="form-check-label fw-bold" for="filters-remote">
                                    Remote only
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.show-current" class="form-check-input me-2" type="checkbox" id="filters-show-current" @if($filters['show-current']) checked @endif>
                                <label class="form-check-label fw-bold" for="filters-show-past">
                                    Future Events
                                </label>
                            </div>
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.show-past" class="form-check-input me-2" type="checkbox" id="filters-show-past" @if($filters['show-past']) checked @endif>
                                <label class="form-check-label fw-bold" for="filters-show-past">
                                    Past Events
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="mb-2 lead">Type</p>
                            @foreach ($typeOptions as $id => $name)
                                <div class="form-check mb-2 d-flex align-items-center">
                                    <input wire:model="filters.selected-types" class="form-check-input mt-0 me-2" type="checkbox" value="{{ $id }}" id="filter-type-{{ $name }}">
                                    <label class="form-check-label fw-bold" for="filter-type-{{ $name }}">{{ $name }}</label>
                                </div>
                            @endforeach
                        </div>

                        <p class="mb-2 lead">Date</p>
                        <div class="row mb-4">
                            <div class="col-12 col-xl-6 input-group mb-2">
                                <span class="input-group-text">From</span>
                                <input wire:model="filters.date-min" type="date" class="form-control" id="filter-date-min" placeholder="MM/DD/YYYY">
                            </div>
                            <div class="col-12 col-xl-6 input-group mb-2">
                                <span class="input-group-text">To</span>
                                <input wire:model="filters.date-max" type="date" class="form-control" id="filter-date-max" placeholder="MM/DD/YYYY">
                            </div>
                        </div>

                        <p class="mb-2 lead">Records have related</p>
                        <div class="mb-4">
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.has" class="form-check-input mt-0 me-2" type="checkbox" value="organizations" id="filter-has-organizations">
                                <label class="form-check-label fw-bold" for="filter-has-organizations">
                                    Organizations
                                </label>
                            </div>
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.has" class="form-check-input mt-0 me-2" type="checkbox" value="investors" id="filter-has-investors">
                                <label class="form-check-label fw-bold" for="filter-has-investors">
                                    Investors
                                </label>
                            </div>
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.has" class="form-check-input mt-0 me-2" type="checkbox" value="people" id="filter-has-people">
                                <label class="form-check-label fw-bold" for="filter-has-organizations">
                                    People
                                </label>
                            </div>
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.has" class="form-check-input mt-0 me-2" type="checkbox" value="locations" id="filter-has-locations">
                                <label class="form-check-label fw-bold" for="filter-has-locations">
                                    Locations
                                </label>
                            </div>
                        </div>

                        <p class="mb-2 lead">Filter by</p>
                        <div class="mb-4">
                            <label class="form-label fw-bold">People</label>
                            <select id="filters-people" class="form-select" multiple>
                                @foreach($people as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Organizations</label>
                            <select id="filters-organizations" class="form-select" multiple>
                                @foreach($organizations as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Investors</label>
                            <select id="filters-investors" class="form-select" multiple>
                                @foreach($investors as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Locations</label>
                            <select id="filters-locations" class="form-select" multiple>
                                @foreach($locations as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Tags</label>
                            <select id="filters-tags" class="form-select" multiple>
                                @foreach($tags as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <button wire:click="resetFilters" class="btn btn-sm btn-primary">Reset Filters</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--  Records  --}}
        <div class="records-list col-12 col-md-8 col-lg-9 col-xl-10">
            <x-entity-index.sort-total-block :sortOptions="$sortOptions" :total="$records->total()"
                 currentSortOption="{{$currentSortOption}}"></x-entity-index.sort-total-block>
            <div class="row">
                @forelse($records as $event)
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3 card">
                        <a class="card-body d-flex flex-column position-relative"
                           href="{{ route('admin.events.show', $event->slug) }}">
                            <div class="text-center">
                                <div class="image" style="background-image: url({{ $event->image_url }});"></div>
                                <h3 class="h5 text-dark mb-2">{{ $event->name }}</h3>
                                <p class="mb-2 lead text-muted">{{ $event->type }}</p>
                                <p class="mb-2">
                                    <i class="fa-duotone fa-calendar-days me-2" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Date" data-bs-original-title="Date"></i>
                                    @if($event->end_date)
                                        <span>{{ Carbon\Carbon::parse($event->start_date)->format('D M j') }}</span>
                                        <span class="mx-1">&ndash;</span>
                                        <span>{{ Carbon\Carbon::parse($event->end_date)->format('M j, Y') }}</span>
                                    @else
                                        <span>{{ Carbon\Carbon::parse($event->start_date)->format('D M j, Y') }}</span>
                                    @endif
                                </p>
                                @if($event->remote)
                                    <p class="mb-2 text-quaternary">Remote / Virtual</p>
                                @endif
                            </div>

                            @if($event->status === \App\Models\Event::STATUS_PAST)
                                <div class="event-past-label">Past</div>
                            @endif
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="p-5 lead text-secondary fw-bold text-center">No records match your query.</div>
                    </div>
                @endforelse

                <div class="d-flex justify-content-between my-4">
                    <div class="d-flex align-items-center me-4">
                        <select wire:model="perPage" id="perPage" class="form-select">
                            <option value="12">12</option>
                            <option value="24">24</option>
                            <option value="48">48</option>
                        </select>
                        <label for="perPage" class="d-block fw-bold ms-2 flex-shrink-0">Per Page</label>
                    </div>

                    {{ $records->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('end')
    <script>
        const filterPeople = document.getElementById('filters-people');
        const filterPeopleChoices= new Choices(filterPeople, {
            removeItemButton: true,
        });

        filterPeople.addEventListener('change', () => {
            @this.set('filters.people', filterPeopleChoices.getValue(true));
        });

        const filterOrganizations = document.getElementById('filters-organizations');
        const filterOrganizationsChoices= new Choices(filterOrganizations, {
            removeItemButton: true,
        });

        filterOrganizations.addEventListener('change', () => {
            @this.set('filters.organizations', filterOrganizationsChoices.getValue(true));
        });

        const filterInvestors = document.getElementById('filters-investors');
        const filterInvestorsChoices= new Choices(filterInvestors, {
            removeItemButton: true,
        });

        filterInvestors.addEventListener('change', () => {
            @this.set('filters.organizations', filterInvestorsChoices.getValue(true));
        });

        const filterLocations = document.getElementById('filters-locations');
        const filterLocationsChoices= new Choices(filterLocations, {
            removeItemButton: true,
        });

        filterLocations.addEventListener('change', () => {
            @this.set('filters.locations', filterLocationsChoices.getValue(true));
        });

        const filterTags = document.getElementById('filters-tags');
        const filterTagsChoices= new Choices(filterTags, {
            removeItemButton: true,
        });

        filterTags.addEventListener('change', () => {
            @this.set('filters.tags', filterTagsChoices.getValue(true));
        });
    </script>
@endpush
