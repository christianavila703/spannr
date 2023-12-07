<div>
    <div class="mt-4 d-flex">
        <div class="mb-4 me-4 d-flex align-items-center">
            <input wire:model.lazy="search" type="text" class="form-control" placeholder="Search...">
            @if($search) <button wire:click="clearSearch" class="btn btn-sm text-danger ms-1"><i class="fa-sharp fa-solid fa-xmark"></i></button> @endif
        </div>

        <div class="filter-widget me-md-4 mb-3">
            <div class="btn-group">
                <button type="button" class="btn btn-md @if($filters['status']) btn-accent @else btn-primary @endif btn-primary dropdown-toggle rounded-0" data-bs-toggle="dropdown" aria-expanded="false">
                    Status
                </button>
                <ul class="dropdown-menu" style="min-width: 200px">
                    @foreach ($statusOptions as $optionId => $option)
                        <li class="px-3">
                            <div class="form-check form-check-small form-check-inline">
                                <input wire:model="filters.status" class="form-check-input" type="checkbox" value="{{ $option }}"
                                       id="filter-status-{{ $optionId }}" @if(in_array($option, $filters['status'])) checked @endif>
                                <label class="form-check-label @if(in_array($option, $filters['status'])) fw-bold @endif" for="filter-status-{{ $optionId }}">
                                    {{ $option }}
                                </label>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="filter-widget me-md-4 mb-3">
            <div class="btn-group">
                <button type="button" class="btn btn-md @if($filters['treatment']) btn-accent @else btn-primary @endif btn-primary dropdown-toggle rounded-0" data-bs-toggle="dropdown" aria-expanded="false">
                    Treatments
                </button>
                <ul class="dropdown-menu" style="min-width: 250px">
                    @foreach ($treatmentOptions as $option)
                        <li class="px-3">
                            <div class="form-check form-check-small form-check-inline">
                                <input wire:model="filters.treatment" class="form-check-input" type="checkbox" value="{{ $option['name'] }}"
                                       id="filter-treatment-{{ $option['slug'] }}" @if(in_array($option['name'], $filters['treatment'])) checked @endif>
                                <label class="form-check-label @if(in_array($option['name'], $filters['treatment'])) fw-bold @endif" for="filter-treatment-{{ $option['slug'] }}">
                                    {{ $option['name'] }}
                                </label>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="filter-widget me-md-4 mb-3">
            <div class="btn-group">
                <button type="button" class="btn btn-md @if($filters['type']) btn-accent @else btn-primary @endif btn-primary dropdown-toggle rounded-0" data-bs-toggle="dropdown" aria-expanded="false">
                    Provider Type
                </button>
                <ul class="dropdown-menu" style="min-width: 200px">
                    @foreach ($typeOptions as $optionId => $option)
                        <li class="px-3">
                            <div class="form-check form-check-small form-check-inline">
                                <input wire:model="filters.type" class="form-check-input" type="checkbox" value="{{ $option }}"
                                       id="filter-type-{{ $optionId }}" @if(in_array($option, $filters['type'])) checked @endif>
                                <label class="form-check-label @if(in_array($option, $filters['type'])) fw-bold @endif" for="filter-type-{{ $optionId }}">
                                    {{ $option }}
                                </label>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="filter-widget me-md-4">
            <div class="form-check lead" style="padding-top: 4px">
                <input wire:model="virtual" class="form-check-input" type="checkbox" id="filter-virtual">
                <label class="form-check-label" for="filter-virtual">
                    Telehealth / Virtual
                </label>
            </div>
        </div>
        <div>
            <button wire:click="clearFilters" class="btn">Clear Filters</button>
        </div>
    </div>
    <div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th><x-livewire-filters.sort-button label="Name" field="name" :sorts="$sorts" /></th>
                        <th><x-livewire-filters.sort-button label="Type" field="type" :sorts="$sorts" /></th>
                        <th><x-livewire-filters.sort-button label="Status" field="status" :sorts="$sorts" /></th>
                        <th>Phone / Website</th>
                        <th>Insurance / Virtual</th>
                        <th>Related Entities</th>
                        <th>Listing Completion</th>
                        <th>Interactivity Stats</th>
                        <th><x-livewire-filters.sort-button label="Created" field="created_at" :sorts="$sorts" /></th>
                        <th><x-livewire-filters.sort-button label="Updated" field="updated_at" :sorts="$sorts" /></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $listing)
                        <tr wire:key="record-{{ $listing->slug }}">
                            <td>
                                <a href="{{ $listing->show_url }}">{{ $listing->name }}</a>
                            </td>
                            <td>{{ $listing->type }}</td>
                            <td>
                                <span class="badge {{ $listing->status_color }}">
                                    {{ $listing->status }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $listing->phone }}</div>
                                <div class="small">
                                    <a href="{{ $listing->website }}" target="_blank" rel="noopener noreferrer">{{ $listing->website }}</a>
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <div>@include('public.marketplace.badges.insurance')</div>
                                    <div>@include('public.marketplace.badges.virtual')</div>
                                </div>
                            </td>
                            <td>
                                @if($listing->relatable)
                                    <div>
                                        <span class="d-block"><i class="fa-sharp fa-solid fa-star text-quaternary"></i> {{ $listing->relatable->name }}</span>
                                        <span class="small text-muted">{{ $listing->relatable_type }}</span>
                                    </div>
                                @endif
                                <div class="mt-2 small">
                                    @foreach($listing->connectedListings as $connectedListing)
                                        <div>
                                            <i class="{{ $connectedListing->type_icon }} me-1 fa-fw text-secondary"></i>
                                            <a href="{{ $connectedListing->show_url }}">{{ $connectedListing->name }}</a>
                                        </div>
                                    @endforeach
                                    @foreach($listing->people as $person)
                                        <div>
                                            <i class="fa-duotone fa-duotone fa-user-hair-buns me-1 fa-fw text-secondary"></i>
                                            <a href="{{ route('admin.people.show', $person->slug) }}">{{ $person->name }}</a>
                                        </div>
                                    @endforeach
                                    @foreach($listing->organizations as $organization)
                                        <div>
                                            <i class="fa-duotone fa-buildings me-1 fa-fw text-secondary"></i>
                                            <a href="{{ route('admin.organizations.show', $organization->slug) }}">{{ $organization->name }}</a>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                @php
                                    $relationships = [
                                        'Treatments' => $listing->treatments_count,
                                        'Focus' => $listing->focus_count,
                                        'Languages' => $listing->languages_count,
                                        'Addresses' => $listing->addresses_count,
                                        'Age Groups' => $listing->age_groups_count
                                    ];
                                @endphp
                                <table class="table table-sm small w-auto table-borderless m-0">
                                    @foreach($relationships as $label => $count)
                                        <tr>
                                            <td class="text-end py-0">{{ $label }}</td>
                                            <td class="py-0">
                                                <span class="{{ ($count > 0) ? 'fw-bold' : 'text-danger' }}">{{ $count }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="py-0">Primary Address</td>
                                        <td class="py-0">
                                            @if($listing->primaryAddress)
                                                <span class="text-success"><i class="fa-sharp fa-solid fa-check"></i></span>
                                            @else
                                                <span class="text-danger"><i class="fa-sharp fa-solid fa-triangle-exclamation"></i></span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                @php
                                    $stats = [
                                        'Marketplace Clicks' => $listing->listing_clicks_count,
                                        'Listing Emails' => $listing->form_entries_count,
                                        'Website Visits' => $listing->website_visits_count
                                    ];
                                @endphp
                                <table class="table table-sm small w-auto table-borderless m-0">
                                    @foreach($stats as $label => $count)
                                        <tr>
                                            <td class="text-end">{{ $label }}</td>
                                            <td>
                                                <span class="{{ ($count > 0) ? 'fw-bold' : 'opacity-25' }}">{{ $count }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                            <td class="small">{{ $listing->pretty_created_date }}</td>
                            <td class="small">{{ $listing->pretty_updated_date }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No records match your query</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="my-5">
        {{ $records->links() }}
    </div>
    <div class="mt-3 w-auto d-flex">
        <div class="border">
            <div class="text-uppercase fw-bold m-2 text-tertiary">Legend</div>
            <div class="small m-2"><strong>Marketplace Clicks:</strong> how many times a user has clicked on the listing from the marketplace</div>
            <div class="small m-2"><strong>Listing Emails:</strong> how many times a user has sent an email through Spannr</div>
            <div class="small m-2"><strong>Website Visits:</strong> how many times a user has visited the listing website link</div>
        </div>
    </div>
</div>
