<div>
    <div class="mb-4 d-flex align-items-center">
        <div class="me-4">
            <input wire:model="filters.search" type="text" class="form-control" placeholder="Search...">
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
            {{ $people->total() }} results
        </div>
    </div>

    <table class="table small">
        <thead>
        <tr>
            <th style="width: 2.25rem"></th>
            <th class="sortable-column">Name
                <x-tables.sortable-button columnName="name" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
            </th>
            <th class="sortable-column">Byline
                <x-tables.sortable-button columnName="byline" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
            </th>
            <th class="sortable-column">Website
                <x-tables.sortable-button columnName="website" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($people as $person)
            <tr wire:key="person-{{ $person->id }}">
                <td style="width: 2.25rem">
                    @if ($person->ownedInvestor)
                        <span class="text-quaternary"><i class="{{ $person->investor_icon }}"></i></span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.people.show', $person->slug) }}">{{ $person->name }}</a>
                </td>
                <td>{{ $person->byline }}</td>
                <td>
                    <a href="{{ $person->website }}" target="_blank" rel="noopener noreferrer">
                        <span class="truncate-200">{{ $person->website }}</span>
                    </a>
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

        {{ $people->links() }}
    </div>
</div>
