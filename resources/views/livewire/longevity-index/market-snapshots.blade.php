<div>
    <div class="mb-4 d-flex flex-wrap">
        <div class="me-4 mb-3">
            <input wire:model="filters.search" type="text" class="form-control" placeholder="Search...">
        </div>
        <div class="me-4 d-flex">
            <div>
                <input wire:model="filters.date-min" type="date" class="form-control" id="filter-date-min" placeholder="MM/DD/YYYY" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
            </div>
            <div>
                <label for="filter-date-min" class="d-block fw-bold m-2 flex-shrink-0">to</label>
                <span class="d-block">&nbsp;</span>
            </div>
            <div>
                <input wire:model="filters.date-max" type="date" class="form-control" id="filter-date-max" placeholder="MM/DD/YYYY" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
            </div>
        </div>
        <div class="d-flex me-4 my-1">
            @foreach ($typeOptions as $type)
                <div class="form-check me-3">
                    <input wire:model="filters.selected-types" class="form-check-input" type="checkbox"
                           value="{{ $type }}" id="filter-type-{{ $type }}" @if(in_array($type, $filters['selected-types'])) checked @endif>
                    <label class="form-check-label" for="filter-type-{{ $type }}">
                        {{ ucwords($type) }}
                    </label>
                </div>
            @endforeach
        </div>
        <div class="me-3">
            <button wire:click="resetFilters" class="btn btn-sm">Reset Filters</button>
        </div>
        <div class="text-muted small mt-1">
            {{ $marketSnapshots->total() }} snapshots
        </div>
    </div>

    <div class="table-responsive">
        <table class="table width-auto">
            <thead>
            <tr>
                <th class="min-width-180">Date/Time</th>
                <th class="min-width-150">Snapshot Type</th>
                <th class="min-width-180 text-end">Market Cap</th>
                <th class="min-width-120 text-end">Index Value</th>
                <th class="min-width-120 text-end">Index &Delta;</th>
                <th class="min-width-120 text-end">Cumulative &Delta;</th>
            </tr>
            </thead>
            <tbody>
            @foreach($marketSnapshots as $marketSnapshot)
                <tr>
                    <td>
                        <a href="{{ route('admin.longevity-index.show', $marketSnapshot->id) }}">{{ $marketSnapshot->formatted_pretty_date }}</a>
                    </td>
                    <td>
                        <span class="{{ $marketSnapshot->type_color }}">
                            {{ ucwords($marketSnapshot->type) }}
                        </span>
                        @if($marketSnapshot->notes)
                            <i class="fa-solid fa-circle-info mx-2 text-quaternary" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $marketSnapshot->notes }}"></i>
                        @endif
                    </td>
                    <td class="text-end">${{ number_format($marketSnapshot->market_cap, 0) }}</td>
                    <td class="text-end">
                        <span class="{{ $marketSnapshot->index_change_color }}">{{ $marketSnapshot->formatted_index_value }}</span>
                    </td>
                    <td class="text-end">
                        {{ $marketSnapshot->formatted_index_change }}
                    </td>
                    <td class="text-end">
                        {{ $marketSnapshot->formatted_cumulative_change }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex mt-5 align-items-center">
        <div class="d-flex align-items-center me-5 mb-3">
            <select wire:model="perPage" id="perPage" class="form-select">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <label for="perPage" class="d-block fw-bold ms-2 flex-shrink-0">Per Page</label>
        </div>

        <div>
            {{ $marketSnapshots->links() }}
        </div>
    </div>
</div>
