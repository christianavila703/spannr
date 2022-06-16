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
        <div class="ms-auto lead">
            {{ $organizations->total() }} results
        </div>
    </div>
    <table class="table">
        <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Founded</th>
            <th>Website</th>
            <th>Summary</th>
            <th class="text-nowrap">Fundraising Rounds</th>
            <th>Investors</th>
            <th>People</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($organizations as $organization)
            <tr>
                <td class="text-nowrap">
                    <a href="{{ route('organizations.show', $organization->slug) }}">{{ $organization->name }}</a>
                </td>
                <td>{{ $organization->type }}</td>
                <td>{{ $organization->founded_date }}</td>
                <td>
                    <a href="{{ $organization->website }}" target="_blank" rel="noopener noreferrer">
                        {{ $organization->website }}
                    </a>
                </td>
                <td>
                    <div class="max-width-300">
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
                            <a href="{{ route('investors.show', $investor->slug) }}">{{ $investor->name }}</a> @if (!$loop->last) / @endif
                        @endforeach
                    </div>
                </td>
                <td>
                    <div class="max-width-600">
                        @foreach ($organization->people as $person)
                            <a href="{{ route('people.show', $person->slug) }}">{{ $person->name }}</a> @if (!$loop->last) / @endif
                        @endforeach
                    </div>
                </td>
                <td class="text-right">
                    <a href="{{ route('organizations.edit', $organization->id) }}">edit</a>
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
