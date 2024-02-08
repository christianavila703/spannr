<div class="marketplace-listings-container">
    <div class="d-flex flex-column flex-md-row flex-wrap align-items-center mt-5 max-width-780 mx-auto">
        <div class="marketplace-location-search-container filter-widget d-flex justify-content-center align-items-center me-md-4 mb-3 flex-grow-1 max-width-420">
            <input wire:model.lazy="search" type="text" class="form-control me-2" placeholder="Search by keyword" aria-label="Search listings" id="marketplace-search-box">
            <span>
                <i class="fa-sharp fa-solid fa-circle-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Search by name, location, treatment, etc."></i>
            </span>
            <div id="marketplace-search-preset-options" class="marketplace-location-results d-none short shadow-sm">
                <ul class="list-group">
                    @foreach($presetOptions as $option)
                        <li class="list-group-item list-group-item-action p-0">
                            <button wire:click="setSearchValue('{{ $option }}')" class="btn checkbox-choice-button small text-start">{{ $option }}</button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="filter-widget d-flex align-items-center me-md-4 mb-3 marketplace-location-search-container max-width-420">
            <input wire:model.live="locationSearch" type="text" class="form-control me-2" placeholder="Search location" aria-label="Search location">
            <div class="input-group me-2" style="max-width: 125px">
                <input wire:model.lazy="locationDistance" type="text" class="form-control" placeholder="100" aria-label="Distance in miles">
                <span class="input-group-text">miles</span>
            </div>
            <span>
                <i class="fa-sharp fa-solid fa-circle-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Search by street, city, zip code, state, etc."></i>
            </span>
            @if($locationResults)
                <div class="marketplace-location-results shadow">
                    <ul class="list-group">
                        @forelse($locationResults as $resultId => $result)
                            <li class="list-group-item list-group-item-action p-0">
                                <button wire:click="selectLocation('{{ $resultId }}')" class="btn checkbox-choice-button small text-start">{{ $result['properties']['formatted'] }}</button>
                            </li>
                        @empty
                            <li class="list-group-item">
                                We weren't able to find a match. Try your search again or contact help@spannr.com
                            </li>
                        @endforelse
                    </ul>
                </div>
            @endif
        </div>
    </div>
    <div class="mt-2 mb-3 max-width-780 mx-auto">
        <button wire:click="searchMarketplace" type="button" class="btn btn-lg btn-primary">Search Marketplace</button>
    </div>
    <div class="d-flex flex-wrap" style="min-height: 55px">
        @if($search)
            <div class="me-3 mb-3">
                <span>{{ $search }}</span>
                <button wire:click="clearSearch" class="btn text-danger px-1 border-0" aria-label="Clear search"><i class="fa-sharp fa-solid fa-circle-xmark"></i></button>
            </div>
        @endif
        @foreach($filters['type'] as $id => $type)
            <div class="me-3 mb-3">
                <span>{{ $type }}</span>
                <button wire:click="clearFilter('type', '{{ $id }}')" class="btn text-danger px-1 border-0" aria-label="Clear"><i class="fa-sharp fa-solid fa-circle-xmark"></i></button>
            </div>
        @endforeach
        @if($virtual)
            <div class="me-3 mb-3">
                <span>Telehealth</span>
                <button wire:click="clearVirtual" class="btn text-danger px-1 border-0" aria-label="Clear"><i class="fa-sharp fa-solid fa-circle-xmark"></i></button>
            </div>
        @endif
        @if($location['name'])
            <div class="me-3 mb-3">
                <span>{{ $location['name'] }}</span>
                <button wire:click="clearLocation" class="btn text-danger px-1 border-0" aria-label="Clear"><i class="fa-sharp fa-solid fa-circle-xmark"></i></button>
            </div>
        @endif
        <div class="filter-widget ms-auto">
            <button wire:click="clearFilters" class="btn btn-sm btn-ghost-primary">Clear Filters</button>
        </div>
    </div>
    <div class="row mt-4">
        @forelse($records as $listing)
            <div wire:key="record-{{ $listing->slug }}" class="col-12 col-md-6 col-lg-4 col-xl-3 mb-4">
                <div class="marketplace-related-card hover text-start h-100">
                    <a wire:click="go('{{ $listing->id }}', '{{ $listing->show_url }}')" href="{{ $listing->show_url }}" class="h-100">
                        <div class="image-entity-logo-200 mx-auto mb-3"
                             style="background-image: url({{ $listing->medium_featured_image_url ?? Vite::asset('resources/images/placeholder-marketplace.png') }});"></div>
                        <h3 class="h4 mb-3">{{ $listing->name }}</h3>
                        <div class="lead text-muted mb-2">
                            <i class="{{ $listing->type_icon }} me-2"></i><span>{{ $listing->type }}</span>
                        </div>
                        <div class="d-flex flex-column small text-muted w-100 flex-grow-1 justify-content-end">
                            <div>
                                @if($location['latitude'] AND $location['longitude'])
                                    <div class="mt-2">
                                        @foreach($listing->addresses as $address)
                                            <div class="small mb-3">
                                                <div style="min-height: 40px">{!! $address->full_address !!}</div>
                                                @if($location['latitude'] AND $location['longitude'])
                                                    <div class="text-tertiary mt-1">{{ $address->getDistance($location['latitude'], $location['longitude']) }} miles</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div>
                                        @if($listing->primaryAddress)
                                            <div class="small mb-3">
                                                <div style="min-height: 40px">{!! $listing->primaryAddress->full_address !!}</div>
                                                <div>
                                                    @if($location['latitude'] AND $location['longitude'])
                                                        <div class="text-tertiary">{{ $listing->distance }} miles</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="row small text-muted" style="min-height: 20px">
                                @if($listing->addresses_count > 1)
                                    <div class="col-12 col-md-6">
                                        <i class="fa-sharp fa-solid fa-building-magnifying-glass fa-fw text-primary me-1"></i>
                                        <span>Multiple Locations</span>
                                    </div>
                                @endif
                                @if($listing->insurance)
                                    <div class="col-12 col-md-6">
                                        @include('public.marketplace.badges.insurance')
                                    </div>
                                @endif
                                @if($listing->virtual)
                                    <div class="col-12 col-md-6">
                                        @include('public.marketplace.badges.virtual')
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @empty
            <div wire:key="empty-record" class="col-12 pb-5">
                @if($formSuccess)
                    <div class="fs-3 text-center fw-bold text-quaternary" style="min-height: 300px">
                        Thanks for your message! We'll be in touch.
                    </div>
                @else
                    <div class="max-width-700 mx-auto">
                        <div class="fs-3 text-center fw-bold text-quaternary">No records match your search criteria.</div>
                        <div class="fw-bold text-center mb-3">We're always expanding our marketplace - let us know what you're looking for!</div>
                        <form wire:submit.prevent="submit" class="px-lg-5">
                            <div class="row">
                                <div class="col-12 col-lg-6 mb-3">
                                    <input type="text" wire:model="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" aria-label="First name" placeholder="First name">
                                    @error('first_name') <div class="small text-danger">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <input type="text" wire:model="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" aria-label="Last name" placeholder="Last name">
                                    @error('last_name') <div class="small text-danger">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <input type="email" wire:model="email" id="email" class="form-control @error('email') is-invalid @enderror" aria-label="Email" placeholder="Email">
                                    @error('email') <div class="small text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <textarea wire:model="message" class="form-control @error('message') is-invalid @enderror" placeholder="Message" aria-label="Message" id="message" style="min-height: 100px"></textarea>
                                @error('message') <div class="small text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary rounded-0">Send</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        @endforelse
    </div>
    <div class="d-flex flex-wrap my-4 justify-content-center">
        {{ $records->links() }}
    </div>

    @push('end')
        <script src="https://unpkg.com/alpinejs" defer></script>
        <script>
            let directoryElement = document.querySelector('.marketplace-listings-container');
            Livewire.on('gotoTop', () => {
                directoryElement.scrollIntoView();
            });
        </script>
    @endpush
</div>
