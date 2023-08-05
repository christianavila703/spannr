<div>
    <div class="mb-4 d-flex align-items-center">
        <div class="me-4">
            <input wire:model="filters.search" type="text" class="form-control" placeholder="Search...">
        </div>
        <div class="me-4 d-flex align-items-center">
            <div class="btn-group">
                <button type="button" class="btn btn-outline-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter by Type
                </button>
                <ul class="dropdown-menu" wire:ignore.self>
                    @foreach ($typeOptions as $option)
                        <li class="px-3">
                            <div class="form-check d-flex align-items-center">
                                <input wire:model="filters.selected-types" class="form-check-input me-2 mt-0" type="checkbox" value="{{ $option }}"
                                       id="filter-type-{{ $option }}">
                                <label class="d-block @if(in_array($option, $filters['selected-types'])) fw-bold @endif" for="filter-type-{{ $option }}">
                                    {{ $option }}
                                </label>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="me-4 d-flex align-items-center">
            <div class="form-check me-3">
                <input wire:model="filters.hasStock" class="form-check-input" type="checkbox" value="1" id="filter-has-stock" @if(isset($filters['hasStock']) && $filters['hasStock']) checked @endif>
                <label class="form-check-label" for="filter-has-stock">
                    <strong>With Ticker</strong>
                </label>
            </div>
        </div>
        <div class="me-4">
            <div class="form-check me-3 d-flex align-items-center">
                <input wire:model="filters.owned-investor" class="form-check-input mt-0 me-2" type="checkbox" value="true" id="filter-owner-investor">
                <label class="form-check-label fw-bold" for="filter-owner-investor">
                    Show Investors
                </label>
            </div>
        </div>
        <div>
            <button wire:click="resetFilters" class="btn btn-sm">Reset Filters</button>
        </div>
        <div class="ms-auto lead">
            {{ $organizations->total() }} results
        </div>
    </div>
    <table class="table small">
        <thead>
        <tr>
            <th style="width: 2.25rem"></th>
            <th class="sortable-column">Name
                <x-tables.sortable-button columnName="name" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
            </th>
            <th class="sortable-column">Type
                <x-tables.sortable-button columnName="type" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}" />
            </th>
            <th class="sortable-column">Longevity
                <x-tables.sortable-button columnName="longevity" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}" />
            </th>
            <th class="sortable-column">Founded
                <x-tables.sortable-button columnName="founded_date" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}" />
            </th>
            <th class="sortable-column">Website
                <x-tables.sortable-button columnName="website" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}" />
            </th>
            <th>Summary</th>
            <th class="text-nowrap">Fundraising Rounds</th>
            <th>Investors</th>
            <th>People</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($organizations as $organization)
            <tr wire:key="organization-{{ $organization->id }}">
                <td style="width: 2.25rem">
                    @if ($organization->ownedInvestor)
                        <span class="text-quaternary"><i class="{{ $organization->investor_icon }}"></i></span>
                    @endif
                </td>
                <td class="text-nowrap">
                    <a href="{{ route('admin.organizations.show', $organization->slug) }}">{{ $organization->name }}</a>
                    @if($organization->stock)
                        <br><span class="badge bg-primary">{{ $organization->stock->ticker }}</span>
                    @endif
                </td>
                <td>
                    <span class="truncate-100">
                        {{ $organization->type }}
                    </span>
                </td>
                <td>
                    <span class="truncate-100">
                        {{ is_null($organization->longevity) ? '' : ($organization->longevity ? 'Yes' : 'No') }}
                    </span>
                </td>
                <td>
                    <span class="truncate-100">{{ $organization->founded_date }}</span>
                </td>
                <td>
                    <a href="{{ $organization->website }}" target="_blank" rel="noopener noreferrer">
                        <span class="truncate-200">{{ $organization->website }}</span>
                    </a>
                </td>
                <td>
                    <div class="max-width-300 truncate-300">
                        {{ $organization->summary }}
                    </div>
                </td>
                <td>
                    <div class="max-width-600">
                        @if($organization->fundraisingRounds->count() > 0)
                            ${{ number_format($organization->fundraisingRounds->sum('amount_raised'), 0) }} ({{ $organization->fundraisingRounds->count() }})
                        @endif
                    </div>
                </td>
                <td>
                    <div class="max-width-600">
                        @foreach ($organization->investors as $investor)
                            <a href="{{ route('admin.investors.show', $investor->slug) }}">{{ $investor->name }}</a> @if (!$loop->last) / @endif
                        @endforeach
                    </div>
                </td>
                <td>
                    <div class="max-width-600">
                        @foreach ($organization->people as $person)
                            <a href="{{ route('admin.people.show', $person->slug) }}">{{ $person->name }}</a> @if (!$loop->last) / @endif
                        @endforeach
                    </div>
                </td>
                <td class="text-right">
                    @if($organization->isLocked && $organization->lockedById !== auth()->user()->id)
                        <x-entities.locked-label :record="$organization" />
                    @else
                        <a href="{{ route('admin.organizations.edit', $organization->id) }}">edit</a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

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

        {{ $organizations->links() }}
    </div>
</div>
