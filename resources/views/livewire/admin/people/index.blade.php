<div class="people-index">
    <button class="btn btn-primary d-md-none text-light" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasResponsive" aria-controls="offcanvasResponsive">
        <i class="fa fa-filter"></i>
    </button>

    <div class="row">
        <div wire:ignore class="col-3 col-md-4 col-lg-3 col-xl-2">
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
                                <input wire:model="filters.owned-investor" class="form-check-input mt-0 me-2" type="checkbox" value="true" id="filter-owner-investor">
                                <label class="form-check-label fw-bold" for="filter-owner-investor">
                                    Show Investors
                                </label>
                            </div>
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

                        <div class="mb-4">
                            <p class="mb-2 lead">Records with</p>
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.with" class="form-check-input mt-0 me-2" type="checkbox" value="email" id="filter-with-email">
                                <label class="form-check-label fw-bold" for="filter-with-email">
                                    Email
                                </label>
                            </div>
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.with" class="form-check-input mt-0 me-2" type="checkbox" value="phone" id="filter-with-phone">
                                <label class="form-check-label fw-bold" for="filter-with-phone">
                                    Phone
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

                        <div class="mb-4">
                            <p class="mb-2 lead">Records have</p>
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input wire:model="filters.has" class="form-check-input mt-0 me-2" type="checkbox" value="locations" id="filter-has-locations">
                                <label class="form-check-label fw-bold" for="filter-has-locations">
                                    Locations
                                </label>
                            </div>
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
                        </div>
                        <div>
                            <button wire:click="resetFilters" class="btn btn-sm">Reset Filters</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-8 col-lg-9 col-xl-10">
            <div class="text-end lead">
                {{ $people->total() }} results
            </div>
            <div class="row">
                @forelse($people as $person)
                    <div wire:key="person-{{ $person->id }}" class="col-12 col-sm-6 col-lg-4 col-xl-3 card">
                        <a class="card-body d-flex flex-column" href="{{ route('admin.people.show', $person->slug) }}">
                            <div class="text-center">
                                <div class="image" style="background-image: url({{ $person->image_url }});"></div>
                                <h3 class="h5 text-dark mb-3">
                                    {{ $person->name }}
                                    @if($person->ownedInvestor)
                                        <i class="{{ $person->investor_icon }}"></i>
                                    @endif
                                </h3>
                                <p>{{ $person->byline }}</p>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="p-5 lead text-secondary fw-bold text-center">No people match your query.</div>
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

                    {{ $people->links() }}
                </div>
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
