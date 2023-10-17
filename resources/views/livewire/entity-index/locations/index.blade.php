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

                        <p class="mb-2 lead">Records have related</p>
                        <div class="mb-4">
                            @foreach($relationNames as $relationName)
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.has" class="form-check-input mt-0 me-2" type="checkbox" value="{{ $relationName }}" id="filter-has-{{$relationName}}">
                                <label class="form-check-label fw-bold" for="filter-has-{{$relationName}}">
                                    {{ ucfirst($relationName) }}
                                </label>
                            </div>
                            @endforeach
                        </div>

                        <p class="mb-2 lead">Filter by</p>
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
                            <label class="form-label fw-bold">People</label>
                            <select id="filters-people" class="form-select" multiple>
                                @foreach($people as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Jobs</label>
                            <select id="filters-jobs" class="form-select" multiple>
                                @foreach($jobs as $item)
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
                <div class="col-12">
                    <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Organizations</th>
                            <th>Investors</th>
                            <th>People</th>
                            <th>Jobs</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($records as $location)
                        <tr>
                            <td class="pe-4">
                                <a href="{{ route($showRouteRootPrefix . '.locations.show', $location->slug) }}">{{ $location->name }}</a>
                            </td>
                            <td>
                                <div class="max-width-400 d-none d-lg-inline">
                                    <x-tables.entity-relations-shortlist :records="$location->organizations" routeName="{{ $showRouteRootPrefix }}.organizations.show"></x-tables.entity-relations-shortlist>
                                </div>
                                <div class="d-lg-none">
                                    {{ $location->organizations_count }}
                                </div>
                            </td>
                            <td>
                                <div class="max-width-400 d-none d-lg-inline">
                                    <x-tables.entity-relations-shortlist :records="$location->investors" routeName="{{ $showRouteRootPrefix }}.investors.show"></x-tables.entity-relations-shortlist>
                                </div>
                                <div class="d-lg-none">
                                    {{ $location->investors_count }}
                                </div>
                            </td>
                            <td>
                                <div class="max-width-400 d-none d-lg-inline">
                                    <x-tables.entity-relations-shortlist :records="$location->people" routeName="{{ $showRouteRootPrefix }}.people.show"></x-tables.entity-relations-shortlist>
                                </div>
                                <div class="d-lg-none">
                                    {{ $location->people_count }}
                                </div>
                            </td>
                            <td>
                                <div class="max-width-400 d-none d-lg-inline">
                                    <x-tables.entity-relations-shortlist :records="$location->jobs" routeName="{{ $showRouteRootPrefix }}.jobs.show"></x-tables.entity-relations-shortlist>
                                </div>
                                <div class="d-lg-none">
                                    {{ $location->jobs_count }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No records match your query.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                </div>
            </div>

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

@push('end')
    <script>
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

        const filterPeople = document.getElementById('filters-people');
        const filterPeopleChoices= new Choices(filterPeople, {
            removeItemButton: true,
        });

        filterPeople.addEventListener('change', () => {
            @this.set('filters.people', filterPeopleChoices.getValue(true));
        });

        const filterJobs = document.getElementById('filters-jobs');
        const filterJobsChoices= new Choices(filterJobs, {
            removeItemButton: true,
        });

        filterJobs.addEventListener('change', () => {
            @this.set('filters.tags', filterJobsChoices.getValue(true));
        });
    </script>
@endpush
