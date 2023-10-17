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
                            <p class="mb-2 lead">Round</p>
                            @foreach ($roundOptions as $round)
                                <div class="form-check mb-2 d-flex align-items-center">
                                    <input wire:model="filters.rounds" class="form-check-input mt-0 me-2" type="checkbox" value="{{ $round }}" id="filter-type-{{ \Illuminate\Support\Str::slug($round) }}">
                                    <label class="form-check-label fw-bold" for="filter-type-{{ \Illuminate\Support\Str::slug($round) }}">{{ $round }}</label>
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

                        <p class="mb-2 lead">Amount Raised</p>
                        <div class="row mb-4">
                            <div class="col-12 col-xl-6 input-group mb-2">
                                <span class="input-group-text">Min</span>
                                <input wire:model="filters.amount-min" type="number" class="form-control" id="filter-amount-min" placeholder="$">
                            </div>
                            <div class="col-12 col-xl-6 input-group mb-2">
                                <span class="input-group-text">Max</span>
                                <input wire:model="filters.amount-max" type="number" class="form-control" id="filter-amount-max" placeholder="$">
                            </div>
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
                    <table class="table small">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Round</th>
                                <th>Amount Raised</th>
                                <th class="d-none d-lg-table-cell">Organization</th>
                                <th class="d-none d-lg-table-cell">Investors</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($records as $fundraisingRound)
                            <tr>
                                <td class="text-nowrap">
                                    {{ $fundraisingRound->date }}
                                </td>
                                <td>
                                    <a href="{{ route($showRouteRootPrefix . '.fundraising-rounds.show', $fundraisingRound->id) }}">{{ $fundraisingRound->name }}</a>
                                </td>
                                <td>
                                    {{ $fundraisingRound->round }}
                                </td>
                                <td>
                                    ${{ number_format($fundraisingRound->amount_raised, 0) }}
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <a href="{{ route($showRouteRootPrefix . '.organizations.show', $fundraisingRound->organization->slug) }}">{{ $fundraisingRound->organization->name }}</a>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <x-tables.entity-relations-shortlist :records="$fundraisingRound->investors" routeName="{{$showRouteRootPrefix}}.investors.show"></x-tables.entity-relations-shortlist>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    No fundraising rounds match your query
                                </td>
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
    </script>
@endpush
