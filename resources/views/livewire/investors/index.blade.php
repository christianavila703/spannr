<div>
    <div class="mb-4 d-flex align-items-center">
        <div class="me-4">
            <input wire:model="filters.search" type="text" class="form-control" placeholder="Search...">
        </div>
        <div class="me-4 d-flex align-items-center">
            <strong class="d-block me-3">Type</strong>
            @foreach ($typeOptions as $type)
                <div class="form-check me-3">
                    <input wire:model="filters.selected-types" class="form-check-input" type="checkbox" value="{{ $type }}" id="filter-type-{{ $type }}" @if(in_array($type, $filters['selected-types'])) checked @endif>
                    <label class="form-check-label" for="filter-type-{{ $type }}">
                        {{ $type }}
                    </label>
                </div>
            @endforeach
        </div>
        <div>
            <button wire:click="resetFilters" class="btn btn-sm">Reset Filters</button>
        </div>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Website</th>
            <th>Summary</th>
            <th>Fundraising Rounds</th>
            <th>Organizations</th>
        </tr>
        </thead>
        <tbody>
        @foreach($investors as $investor)
            <tr>
                <td>
                    <a href="{{ route('investors.show', $investor->slug) }}">{{ $investor->name }}</a>
                </td>
                <td class="text-nowrap">{{ $investor->type }}</td>
                <td>{!! $investor->websiteLink !!}</td>
                <td>{{ $investor->summary }}</td>
                <td>
                    <div class="max-width-600">
                        @foreach ($investor->fundraisingRounds as $fundraisingRound)
                            <a href="{{ route('fundraising-rounds.show', $fundraisingRound->id) }}">{{ $fundraisingRound->name }}</a> @if (!$loop->last) / @endif
                        @endforeach
                    </div>
                </td>
                <td>
                    <div class="max-width-600">
                        @foreach ($investor->organizations as $organization)
                            <a href="{{ route('organizations.show', $organization->slug) }}">{{ $organization->name }}</a> @if (!$loop->last) / @endif
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
