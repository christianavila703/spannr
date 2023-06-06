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
                <ul class="dropdown-menu" wire:ignore.self style="min-width: 200px">
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
        <div>
            <button wire:click="resetFilters" class="btn btn-sm">Reset Filters</button>
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
                <x-tables.sortable-button columnName="type" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
            </th>
            <th class="sortable-column">Website
                <x-tables.sortable-button columnName="website" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
            </th>
            <th>Summary</th>
            <th>Fundraising Rounds</th>
            <th>Organizations</th>
        </tr>
        </thead>
        <tbody>
        @foreach($investors as $investor)
            <tr wire:key="investor-{{ $investor->id }}">
                <td style="width: 2.25rem">
                    @if ($investor->ownable_id)
                        <span class="text-quaternary"><i class="{{ $investor->ownable_icon }}"></i></span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.investors.show', $investor->slug) }}"><span class="truncate-200">{{ $investor->name }}</span></a>
                </td>
                <td class="text-nowrap">
                    <span class="truncate-150">{{ $investor->type }}</span>
                </td>
                <td>
                    <a href="{{ $investor->website }}" target="_blank" rel="noopener noreferrer">
                        <span class="truncate-200">{{ $investor->website }}</span>
                    </a>
                </td>
                <td>
                    @if($investor->ownable_id)
                        <span class="text-warning">
                            <i class="{{ $investor->ownable_icon }}"></i>
                        </span>
                        <a href="{{ $investor->ownable_admin_link }}">{{ $investor->ownable_name }}</a>
                    @else
                        <div class="truncate-350">
                            {{ $investor->summary }}
                        </div>
                    @endif
                </td>
                <td>
                    <div class="max-width-600">
                        @foreach ($investor->fundraisingRounds as $fundraisingRound)
                            <a href="{{ route('admin.fundraising-rounds.show', $fundraisingRound->id) }}">{{ $fundraisingRound->name }}</a> @if (!$loop->last) / @endif
                        @endforeach
                    </div>
                </td>
                <td>
                    <div class="max-width-600">
                        @foreach ($investor->organizations as $organization)
                            <a href="{{ route('admin.organizations.show', $organization->slug) }}">{{ $organization->name }}</a> @if (!$loop->last) / @endif
                        @endforeach
                    </div>
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

        {{ $investors->links() }}
    </div>
</div>
