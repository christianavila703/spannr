<div class="records-index">
    <button class="btn btn-primary d-md-none text-light" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasResponsive" aria-controls="offcanvasResponsive">
        <i class="fa fa-filter"></i>
    </button>

    {{--  Filters  --}}
    <div class="row">
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
                                <input wire:model="filters.owned-investor" class="form-check-input mt-0 me-2" type="checkbox" value="1" id="filter-owned-investor">
                                <label class="form-check-label fw-bold" for="filter-owned-investor">
                                    Show Investors
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.longevity-related" class="form-check-input mt-0 me-2" type="checkbox" value="1" id="filter-longevity-related">
                                <label class="form-check-label fw-bold" for="filter-longevity-related">
                                    Show Longevity Related
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="mb-2 lead">Type</p>
                            @foreach ($typeOptions as $option)
                                <div class="form-check mb-2 d-flex align-items-center">
                                    <input wire:model="filters.selected-types" class="form-check-input mt-0 me-2" type="checkbox" value="{{ $option }}" id="filter-type-{{ $option }}">
                                    <label class="form-check-label fw-bold" for="filter-type-{{ $option }}">{{ $option }}</label>
                                </div>
                            @endforeach
                        </div>

                        <p class="mb-2 lead">Records with</p>
                        <div class="mb-4">
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.with" class="form-check-input mt-0 me-2" type="checkbox" value="website" id="filter-with-email">
                                <label class="form-check-label fw-bold" for="filter-with-email">
                                    Website
                                </label>
                            </div>
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.with" class="form-check-input mt-0 me-2" type="checkbox" value="facebook" id="filter-with-facebook">
                                <label class="form-check-label fw-bold" for="filter-with-facebook">
                                    Facebook
                                </label>
                            </div>
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.with" class="form-check-input mt-0 me-2" type="checkbox" value="twitter" id="filter-with-twitter">
                                <label class="form-check-label fw-bold" for="filter-with-twitter">
                                    Twitter
                                </label>
                            </div>
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.with" class="form-check-input mt-0 me-2" type="checkbox" value="linkedin" id="filter-with-linkedin">
                                <label class="form-check-label fw-bold" for="filter-with-linkedin">
                                    LinkedIn
                                </label>
                            </div>
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.with" class="form-check-input mt-0 me-2" type="checkbox" value="instagram" id="filter-with-instagram">
                                <label class="form-check-label fw-bold" for="filter-with-instagram">
                                    Instagram
                                </label>
                            </div>
                        </div>

                        <p class="mb-2 lead">Records have related</p>
                        <div class="mb-4">
                            <div class="form-check mb-2">
                                <input wire:model="filters.has" class="form-check-input mt-0 me-2" type="checkbox" value="stock" id="filter-has-stock">
                                <label class="form-check-label fw-bold" for="filter-has-stock">
                                    Ticker
                                </label>
                            </div>
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.has" class="form-check-input mt-0 me-2" type="checkbox" value="locations" id="filter-has-locations">
                                <label class="form-check-label fw-bold" for="filter-has-locations">
                                    Locations
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
                                <label class="form-check-label fw-bold" for="filter-has-people">
                                    People
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
            <x-entity-index.sort-total-block :sortOptions="$sortOptions" :total="$records->total()"></x-entity-index.sort-total-block>
            <div class="row">
                @forelse($records as $organization)
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3 card">
                        <a class="card-body d-flex flex-column" href="{{ route('admin.organizations.show', $organization->slug) }}">
                            <div class="text-center">
                                <div class="image" style="background-image: url({{ $organization->image_url }});"></div>
                                <h3 class="h5 text-dark mb-2">
                                    {{ $organization->name }}
                                    @if($organization->ownedInvestor)
                                        <i class="{{ $organization->investor_icon }}"></i>
                                    @endif
                                </h3>
                                <p class="mb-2">{{ $organization->type }}</p>
                                @if($organization->stock)
                                    <p class="mb-2"><span class="badge bg-primary">{{ $organization->stock->ticker }}</span></p>
                                @endif
                            </div>
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

        const filterInvestors = document.getElementById('filters-investors');
        const filterInvestorsChoices= new Choices(filterInvestors, {
            removeItemButton: true,
        });

        filterInvestors.addEventListener('change', () => {
            @this.set('filters.investors', filterInvestorsChoices.getValue(true));
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
