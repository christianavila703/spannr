<div class="records-index">
    <button class="btn btn-primary d-md-none text-light mb-4" type="button" data-bs-toggle="offcanvas"
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
                            <p class="mb-2 lead">Status</p>
                            @foreach ($statusOptions as $status)
                                <div class="form-check mb-2 d-flex align-items-center">
                                    <input wire:model="filters.statuses" class="form-check-input mt-0 me-2" type="checkbox" value="{{ $status }}" id="filter-statuses-{{ $status }}">
                                    <label class="form-check-label fw-bold" for="filter-statuses-{{ $status }}">{{ $status }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-4">
                            <p class="mb-2 lead">Type</p>
                            @foreach ($typeOptions as $type)
                                <div class="form-check mb-2 d-flex align-items-center">
                                    <input wire:model="filters.types" class="form-check-input mt-0 me-2" type="checkbox" value="{{ $type }}" id="filter-type-{{ $type }}">
                                    <label class="form-check-label fw-bold" for="filter-type-{{ $type }}">{{ $type }}</label>
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

                        <p class="mb-2 lead">Salary</p>
                        <div class="row mb-4">
                            <div class="col-12 col-xl-6 input-group mb-2">
                                <span class="input-group-text">Min</span>
                                <input wire:model="filters.salary-min" type="number" class="form-control" id="filter-salary-min" placeholder="">
                            </div>
                            <div class="col-12 col-xl-6 input-group mb-2">
                                <span class="input-group-text">Max</span>
                                <input wire:model="filters.salary-max" type="number" class="form-control" id="filter-salary-max" placeholder="">
                            </div>
                        </div>

                        <p class="mb-2 lead">Filter by</p>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Categories</label>
                            <select id="filters-categories" class="form-select" multiple>
                                @foreach($categories as $item)
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
        <div class="records-list col-md-8 col-lg-9 col-xl-10 col-12">
            <x-entity-index.sort-total-block :sortOptions="$sortOptions" :total="$records->total()"
                 currentSortOption="{{$currentSortOption}}"></x-entity-index.sort-total-block>

            <div class="jobs-index-container">
                <div class="row">
                    @forelse($records as $job)
                        <div class="@if(!$loop->last) mb-5 @endif">
                            <div data-bs-toggle="offcanvas" data-bs-target="#job-description-{{ $job->slug }}" aria-controls="job-description-{{ $job->slug }}" class="job-posting-item @if($job->monthly) featured @endif text-dark text-decoration-none">
                                <div class="job-posting-item-content">
                                    <h2 class="job-title">
                                        <span>{{ $job->name }}</span>
                                    </h2>
                                    <p class="lead mb-2">{{ $job->owner->name }}</p>
                                    <x-content.jobs.job-summary :job="$job" />
                                </div>
                            </div>

                            <div class="job-description-offcanvas offcanvas offcanvas-bottom" tabindex="-1" id="job-description-{{ $job->slug }}"
                                 aria-labelledby="job-description-{{ $job->slug }}-label">

                                <div class="offcanvas-header border-bottom border-dark">
                                    <div class="offcanvas-header-container">
                                        <div class="h4 mt-1 mb-0 ps-lg-3" id="job-description-{{ $job->slug }}-label">{{ $job->name }}</div>
                                        <div class="pe-2">
                                            <button type="button" class="btn" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-sharp fa-regular fa-xmark fa-lg"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="offcanvas-body">
                                    <div class="pb-5 px-lg-3">
                                        @can('edit records')
                                            <div class="my-4">
                                                <a href="{{ route('admin.jobs.edit', $job->id) }}"
                                                   class="btn btn-primary">Edit</a>
                                            </div>
                                        @endcan
                                        <p class="lead mb-0">{{ $job->owner->name }}</p>
                                        <div class="mt-4">
                                            <x-content.jobs.job-summary :job="$job" />
                                        </div>
                                        <div class="mt-5">
                                            <h2 class="h5 border-bottom border-dark">Job Description</h2>
                                            <div>
                                                @if ($job->description)
                                                    <div>{!! $job->description !!}</div>
                                                @else
                                                    <div>{!! $job->trixRender('description') !!}</div>
                                                @endif
                                            </div>
                                            @if ($job->qualifications)
                                                <h2 class="h5 border-bottom border-dark">Minimum Qualifications</h2>
                                                <div>{!! $job->qualifications !!}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="p-5 lead text-secondary fw-bold text-center">No records match your query.</div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="row">
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
        const filterCategories = document.getElementById('filters-categories');
        const filterCategoriesChoices= new Choices(filterCategories, {
            removeItemButton: true,
        });

        filterCategories.addEventListener('change', () => {
        @this.set('filters.categories', filterCategoriesChoices.getValue(true));
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
