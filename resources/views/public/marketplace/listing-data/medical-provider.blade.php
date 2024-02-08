<div class="row">
    <div class="col-12 col-lg-6 mb-4 mb-lg-0 order-1">
        <h1>{{ $listing->name }}</h1>
        <div class="d-lg-none">
            @if($listing->medium_featured_image_url)
                <div class="mb-4">
                    <img src="{{ $listing->medium_featured_image_url }}" alt="{{ $listing->name }}" loading="lazy">
                </div>
            @endif
        </div>
        <div class="d-md-flex align-items-center text-muted mt-3">
            <div class="mb-3 me-4">
                @include('public.marketplace.badges.type-icon')
            </div>
            <div class="mb-3 me-4">
                @include('public.marketplace.badges.insurance')
            </div>
            <div class="mb-3 me-4">
                @include('public.marketplace.badges.virtual')
            </div>
        </div>
        <p class="lead max-width-700 mb-3">{{ $listing->summary }}</p>
        @if($listing->description)
            <div wire:ignore class="has-collapse-arrows">
                <div class="border-bottom mb-4">
                    <button class="btn btn-lg" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse-listing-description" aria-expanded="true" aria-controls="collapse-listing-description">
                        <span class="icon"></span><span>Details</span>
                    </button>
                </div>
                <div class="collapse show" id="collapse-listing-description">
                    <article class="marketplace-listing-description">
                        {!! $listing->description !!}
                    </article>
                </div>
            </div>
        @endif
        @if($listing->primaryAddress)
            <div id="listing-location-map">
                <h3 class="pt-5">Location</h3>
                <div class="text-secondary d-md-flex flex-wrap justify-content-between fw-bold mb-2">
                    <div>
                        <i class="fa-sharp fa-strong fa-location-dot me-2"></i>
                        <span class="me-3">{{ $listing->primaryAddress->name }}</span>
                    </div>
                    <a href="{{ $listing->primaryAddress->google_maps_url }}" class="text-decoration-none" target="_blank" rel="noopener noreferrer">
                        <span class="me-1">Directions</span>
                        <i class="fa-sharp fa-regular fa-arrow-up-right-from-square"></i>
                    </a>
                </div>
                <div wire:ignore>
                    <div id="listing-map"
                         data-latitude="{{ $listing->primaryAddress->latitude }}"
                         data-longitude="{{ $listing->primaryAddress->longitude }}"
                         data-name="{{ $listing->name }}"
                         data-address="{{ $listing->primaryAddress->name }}"
                    >
                    </div>
                </div>
            </div>
        @endif
        @if($listing->otherAddresses->count() > 0)
            <div wire:key="listing-other-addresses" class="mt-4">
                <h2 class="h5">Other Locations</h2>
                <div class="row">
                    @foreach($listing->otherAddresses as $address)
                        <div class="col-12 mt-2">
                            <i class="fa-sharp fa-solid fa-location-dot fa-lg me-2 text-muted"></i>
                            <a href="{{ $address->google_maps_url }}" target="_blank" rel="noopener noreferrer" class="fw-bold text-decoration-none">{{ $address->name }}</a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($listing->relatable OR $listing->people->count() > 0 OR $listing->connectedListings->count() > 0)
            <div class="mt-5 row">
                @if($listing->relatable)
                    <div class="col-12 col-md-6 col-xl-4 text-center mb-4">
                        <div class="marketplace-related-card hover">
                            <a href="{{ route('public.content.organizations.show', $listing->relatable->slug) }}">
                                <div class="image-entity-logo-200 mx-auto" style="background-image: url({{ $listing->relatable->image_url }});"></div>
                                <div class="h6 mt-3 mb-0">{{ $listing->relatable->name }}</div>
                                <div class="text-muted">
                                    @if($listing->type === \App\Models\Listing::TYPE_CLINIC)
                                        {{ $listing->relatable->type }} Organization
                                    @endif
                                </div>
                            </a>
                        </div>
                    </div>
                @endif
                @foreach($listing->people as $person)
                    <div class="col-12 col-md-6 col-xl-4 text-center mb-4">
                        <div class="marketplace-related-card">
                            <div class="image-entity-logo-200 mx-auto" style="background-image: url({{ $person->image_url }});"></div>
                            <div class="h6 mt-3 mb-0">{{ $person->name }}</div>
                            <div class="text-muted">{{ $person->pivot->label }}</div>
                        </div>
                    </div>
                @endforeach
                @foreach($listing->connectedListings as $connectedListing)
                    <div class="col-12 col-md-6 col-xl-4 text-center mb-4">
                        <div class="marketplace-related-card hover">
                            <a href="{{ $connectedListing->show_url }}">
                                <div class="image-entity-logo-200 mx-auto"
                                     style="background-image: url({{ $connectedListing->medium_featured_image_url ?? Vite::asset('resources/images/placeholder-marketplace.png') }});"></div>
                                <div class="h6 mt-3 mb-0">{{ $connectedListing->name }}</div>
                                <div class="text-muted">{{ $connectedListing->pivot->label }}</div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <div class="col-12 col-lg-6 col-xl-5 offset-xl-1 mb-5 order-2">
        @if($listing->featured_image_url)
            <div class="mb-4 d-none d-lg-block">
                <img src="{{ $listing->featured_image_url }}" alt="{{ $listing->name }}" loading="lazy">
            </div>
        @endif
        <div class="marketplace-listing-connect-box ms-lg-auto">
            <div class="icon-group">
                <svg xmlns="http://www.w3.org/2000/svg" width="19.695" height="23.969" viewBox="0 0 19.695 23.969">
                    <g id="Spannr_logo_mark_Color" transform="translate(-96.28 -0.49)">
                    <path id="Path_179" data-name="Path 179" d="M652.876,206.01a1.448,1.448,0,1,0,0,2.9h.517a1.448,1.448,0,1,0,0-2.9Z" transform="translate(-542.82 -200.959)" fill="#0ac6ff"/>
                    <path id="Path_180" data-name="Path 180" d="M125.626,757.355a1.448,1.448,0,1,0,0-2.9h-.239a1.448,1.448,0,1,0,0,2.9Z" transform="translate(-27.036 -737.237)" fill="#0ac6ff"/>
                    <path id="Path_181" data-name="Path 181" d="M112.718,10.281a7.959,7.959,0,0,0-4.494-1.129c-.478,0-2.317.006-4.035.014l-2.3.009c-.586,0-.873,0-1.014.022V9.178a.661.661,0,0,1-.469-.193.651.651,0,0,1-.194-.465.663.663,0,0,1,.664-.659l5.572,0A1.452,1.452,0,0,0,107.9,6.419a1.428,1.428,0,0,0-.427-1.022,1.449,1.449,0,0,0-1.028-.424h-1.608a.659.659,0,1,1,0-1.319l7.681,0a1.259,1.259,0,0,0,.394-.08,1.581,1.581,0,0,0,0-3.01,1.557,1.557,0,0,0-.4-.077h-8.35a7.826,7.826,0,0,0-4.652,14.159,6.8,6.8,0,0,0,3.216,1.137c.376,0,1.4,0,2.609.008l.1-.012a1.346,1.346,0,0,0-.337-2.651h-.643l-.045,0a.562.562,0,0,1,.045-1.122H109.8a.562.562,0,1,1,0,1.124h-1.112a1.355,1.355,0,0,0-1.357,1.349,1.336,1.336,0,0,0,.4.954,1.37,1.37,0,0,0,.771.376l2.483.019a.692.692,0,1,1,0,1.384h-9.043a1.478,1.478,0,1,0,0,2.955h4.785l.055,0a.692.692,0,0,1-.055,1.382H99.994a.617.617,0,0,0-.1-.008h-.522a1.457,1.457,0,1,0,0,2.914h8.719a7.867,7.867,0,0,0,7.881-7.835A7.258,7.258,0,0,0,112.718,10.281Z" transform="translate(0 0)" fill="#0ac6ff"/>
                    </g>
                </svg>
            </div>
            <div class="form-container">
                <div class="text-content">
                    <p class="h4 title">Connect</p>
                    <p class="description">Get in touch with {{ $listing->name }}</p>
                </div>
                <div class="mb-3">
                    <a href="tel:{{ $listing->phone }}" class="lead d-flex align-items-center text-decoration-none text-body">
                        <div>
                            <i class="fa-sharp fa-solid fa-phone fa-lg"></i>
                        </div>
                        <div class="pb-1 ps-2">
                            {{ $listing->phone }}
                        </div>
                    </a>
                </div>
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <button class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#sendEmailModal">Send Email</button>
                    </div>
                    @if($listing->website)
                        <div class="flex-grow-1">
                            <livewire:public.marketplace.show-listing.website-visit :listing="$listing" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="mt-5">
            @if($listing->certifications)
                <div wire:key="listing-certifications" class="mb-4">
                    <h2 class="h5">Credentials</h2>
                    <ul class="list-group list-group-flush">
                        @foreach($listing->certifications as $certification)
                            <li class="list-group-item px-0">
                                {{ $certification }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if($listing->licenses)
                <div wire:key="listing-licenses" class="mb-4">
                    <h2 class="h5">Licenses</h2>
                    <ul class="list-group list-group-flush">
                        @foreach($listing->licenses as $license)
                            <li class="list-group-item px-0">
                                {{ $license }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if($listing->treatments->count() > 0)
                <div wire:key="listing-treatments" class="mb-4">
                    <h2 class="h5">Treatments / Services</h2>
                    <div class="row">
                        @foreach($listing->treatments as $treatment)
                            <div class="col-12 col-md-6 mt-2">
                                <i class="fa-sharp fa-solid fa-check fa-lg me-2 text-muted"></i>
                                <span>{{ $treatment->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            @if($listing->languages->count() > 0)
                <div wire:key="listing-languages" class="mb-4">
                    <h2 class="h5">Languages</h2>
                    <div class="row">
                        @foreach($listing->languages as $language)
                            <div class="col-12 col-md-6 mt-2">
                                <i class="fa-sharp fa-solid fa-check fa-lg me-2 text-muted"></i>
                                <span>{{ $language->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            @if($listing->ageGroups->count() > 0)
                <div wire:key="listing-age-groups" class="mb-4">
                    <h2 class="h5">Age Groups</h2>
                    <div class="row">
                        @foreach($listing->ageGroups as $ageGroup)
                            <div class="col-12 col-md-6 mt-2">
                                <i class="fa-sharp fa-solid fa-check fa-lg me-2 text-muted"></i>
                                <span>{{ $ageGroup->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div wire:ignore>
        <div class="modal fade" id="sendEmailModal" tabindex="-1" aria-labelledby="sendEmailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sendEmailModalLabel">Send an email to {{ $listing->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <livewire:forms.marketplace.listing-email :listing="$listing" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
