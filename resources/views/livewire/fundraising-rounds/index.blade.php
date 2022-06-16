<div>
    <div class="mb-4 d-flex flex-wrap">
        <div class="me-4">
            <input wire:model="filters.search" type="text" class="form-control" placeholder="Search...">
            <span class="d-block ms-2 text-muted mt-1">
                @if ($filters['search'])
                    <span>{{ $filters['search'] }}</span>
                    <span wire:click="resetFilter('search')" class="clear-value"><i class="fa-solid fa-circle-xmark"></i></span>
                @endif
            </span>
        </div>
        <div class="me-4 d-flex">
            <div>
                <input wire:model="filters.date-min" type="date" class="form-control" id="filter-date-min" placeholder="MM/DD/YYYY">
                <span class="d-block ms-2 text-muted mt-1">
                    @if ($filters['date-min'])
                        <span>{{ \Carbon\Carbon::parse($filters['date-min'])->format('M d, Y') }}</span>
                        <span wire:click="resetFilter('date-min')" class="clear-value"><i class="fa-solid fa-circle-xmark"></i></span>
                    @endif
                </span>
            </div>
            <div>
                <label for="filter-date-min" class="d-block fw-bold m-2 flex-shrink-0">to</label>
                <span class="d-block">&nbsp;</span>
            </div>
            <div>
                <input wire:model="filters.date-max" type="date" class="form-control" id="filter-date-max" placeholder="MM/DD/YYYY">
                <span class="d-block ms-2 text-muted mt-1">
                    @if ($filters['date-max'])
                        <span>
                            {{ \Carbon\Carbon::parse($filters['date-max'])->format('M d, Y') }}
                        </span>
                        <span wire:click="resetFilter('date-max')" class="clear-value"><i class="fa-solid fa-circle-xmark"></i></span>
                    @endif
                </span>
            </div>
        </div>
        <div class="me-4 d-flex">
            <div>
                <input wire:model="filters.amount-min" type="number" class="form-control" id="filter-amount-min" placeholder="$">
                <span class="d-block ms-2 text-muted mt-1">
                    @if ($filters['amount-min'])
                        <span>${{ number_format($filters['amount-min'], 0) }}</span>
                        <span wire:click="resetFilter('amount-min')" class="clear-value"><i class="fa-solid fa-circle-xmark"></i></span>
                    @endif
                </span>
            </div>
            <div>
                <label for="filter-amount-min" class="d-block fw-bold m-2 flex-shrink-0">to</label>
                <span class="d-block">&nbsp;</span>
            </div>
            <div>
                <input wire:model="filters.amount-max" type="number" class="form-control" id="filter-amount-max" placeholder="$">
                <span class="d-block ms-2 text-muted mt-1">
                    @if ($filters['amount-max'])
                        <span>${{ number_format($filters['amount-max'], 0) }}</span>
                        <span wire:click="resetFilter('amount-max')" class="clear-value"><i class="fa-solid fa-circle-xmark"></i></span>
                    @endif
                </span>
            </div>
        </div>
        <div>
            <button wire:click="resetFilters" class="btn">Reset Filters</button>
        </div>
    </div>
    <table class="table">
        <thead>
        <tr>
            <th>Date</th>
            <th>Name</th>
            <th>Round</th>
            <th>Amount Raised</th>
            <th>Organization</th>
            <th>Investors</th>
        </tr>
        </thead>
        <tbody>
        @forelse($fundraisingRounds as $fundraisingRound)
            <tr>
                <td class="text-nowrap">
                    {{ $fundraisingRound->date }}
                </td>
                <td>
                    <a href="{{ route('fundraising-rounds.show', $fundraisingRound->id) }}">{{ $fundraisingRound->name }}</a>
                </td>
                <td>
                    {{ $fundraisingRound->round }}
                </td>
                <td>
                    ${{ number_format($fundraisingRound->amount_raised, 0) }}
                </td>
                <td>
                    <a href="{{ route('organizations.show', $fundraisingRound->organization->slug) }}">{{ $fundraisingRound->organization->name }}</a>
                </td>
                <td>
                    <div class="max-width-600">
                        @foreach ($fundraisingRound->investors as $investor)
                            <a href="{{ route('investors.show', $investor->slug) }}">{{ $investor->name }}</a> @if (!$loop->last) / @endif
                        @endforeach
                    </div>
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

        {{ $fundraisingRounds->links() }}
    </div>
</div>
