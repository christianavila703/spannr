<div>
    <div class="mb-4 d-flex align-items-center">
        <div class="me-4">
            <input wire:model="filters.search" type="text" class="form-control" placeholder="Search...">
        </div>
        <div class="me-4 d-flex align-items-center">
            <div class="btn-group">
                <button type="button" class="btn btn-outline-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter by related records
                </button>
                <ul class="dropdown-menu" wire:ignore.self style="min-width: 200px">
                    @foreach ($relationNames as $relation)
                        <li class="px-3">
                            <div class="form-check d-flex align-items-center">
                                <input wire:model="filters.has-relations" class="form-check-input me-2 mt-0" type="checkbox" value="{{ $relation }}"
                                       id="filter-type-{{ $relation }}">
                                <label class="d-block @if(in_array($relation, $filters['has-relations'])) fw-bold @endif" for="filter-type-{{ $relation }}">
                                    {{ ucfirst($relation) }}
                                </label>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div>
            <button wire:click="resetFilters" class="btn btn-sm">Reset Filters</button>
        </div>
        <div class="ms-auto lead">
            {{ $locations->total() }} results
        </div>
    </div>
    <table class="table small">
        <thead>
            <tr>
                <th class="sortable-column">Name
                    <x-tables.sortable-button columnName="name" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
                </th>
                <th class="sortable-column">
                    Organizations
                    <x-tables.sortable-button columnName="organizations_count" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
                </th>
                <th class="sortable-column">
                    Investors
                    <x-tables.sortable-button columnName="investors_count" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
                </th>
                <th class="sortable-column">
                    People
                    <x-tables.sortable-button columnName="people_count" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
                </th>
                <th class="sortable-column">
                    Jobs
                    <x-tables.sortable-button columnName="jobs_count" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
                </th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($locations as $location)
            <tr wire:key="organization-{{ $location->id }}">
                <td class="text-nowrap pe-4">
                    <a href="{{ route('admin.locations.show', $location->id) }}">{{ $location->name }}</a>
                </td>
                <td>
                    <div class="max-width-400">
                        <x-tables.entity-relations-shortlist :records="$location->organizations" routeName="admin.organizations.show"></x-tables.entity-relations-shortlist>
                    </div>
                </td>
                <td>
                    <div class="max-width-400">
                        <x-tables.entity-relations-shortlist :records="$location->investors" routeName="admin.investors.show"></x-tables.entity-relations-shortlist>
                    </div>
                </td>
                <td>
                    <div class="max-width-400">
                        <x-tables.entity-relations-shortlist :records="$location->people" routeName="admin.people.show"></x-tables.entity-relations-shortlist>
                    </div>
                </td>
                <td>
                    <div class="max-width-400">
                        <x-tables.entity-relations-shortlist :records="$location->jobs" routeName="admin.jobs.show"></x-tables.entity-relations-shortlist>
                    </div>
                </td>
                <td class="text-right">
                    @if($location->isLocked && $location->lockedById !== auth()->user()->id)
                        <x-entities.locked-label :record="$location" />
                    @else
                        <a href="{{ route('admin.locations.edit', $location->id) }}">edit</a>
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

        {{ $locations->links() }}
    </div>
</div>
