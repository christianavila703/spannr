<div>
    <div>
        <form wire:submit.prevent="save">
            <div>
                @if($errors->count() > 0)
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="d-flex flex-wrap mb-3">
                <div class="me-2">
                    <label for="market_snapshot_market_cap" class="fw-bold">Market Cap</label>
                    <input type="text" wire:model="marketSnapshot.market_cap" class="form-control" id="market_snapshot_market_cap">
                </div>
                <div class="me-2">
                    <label for="market_snapshot_index_change" class="fw-bold">Index Change</label>
                    <input type="text" wire:model="marketSnapshot.index_change" class="form-control" id="market_snapshot_index_change">
                </div>
                <div class="me-2">
                    <label for="market_snapshot_index_value" class="fw-bold">Index Value</label>
                    <input type="text" wire:model="marketSnapshot.index_value" class="form-control" id="market_snapshot_index_value">
                </div>
                <div class="me-2">
                    <label for="market_snapshot_cumulative_change" class="fw-bold">Cumulative Change</label>
                    <input type="text" wire:model="marketSnapshot.cumulative_change" class="form-control" id="market_snapshot_cumulative_change">
                </div>
                <div>
                    <label for="market_snapshot_notes" class="fw-bold">Notes</label>
                    <input type="text" wire:model="marketSnapshot.notes" class="form-control" id="market_snapshot_notes" name="market_snapshot_notes">
                </div>
            </div>
            <div class="d-flex align-items-center">
                <button type="submit" class="btn btn-tertiary" data-bs-dismiss="modal">Save</button>
                <div class="ms-3">
                    @if ($success)
                        <span class="text-success fw-bold text-uppercase">SAVED!</span>
                    @endif
                </div>
            </div>
        </form>
    </div>
    <table class="table">
        <thead>
        <tr>
            <th>Name</th>
            <th>Weight</th>
            <th>Ticker</th>
            <th>Current Price</th>
            <th>Yesterday Close</th>
            <th>Market Cap</th>
            <th>Volume</th>
            <th>Today's High</th>
            <th>Today's Low</th>
            <th><span class="d-block">Previous Day</span> Price Change</th>
            <th><span class="d-block">Previous Day</span> % Change</th>
            <th>Outstanding Shares</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($marketSnapshot->stockSnapshots as $snapshot)
            <tr :wire:key="snapshot-{{ $marketSnapshot->id }}" wire:poll>
                <td>
                    <a href="{{ route('admin.organizations.show', $snapshot->stock->organization->slug) }}">{{ $snapshot->stock->name }}</a>
                </td>
                <td>
                    {{ $snapshot->weight_percent }}%
                </td>
                <td>{{ $snapshot->stock->ticker }}</td>
                <td>${{ number_format($snapshot->current_price, 2) }}</td>
                <td>${{ number_format($snapshot->yesterday_close, 2) }}</td>
                <td>${{ number_format($snapshot->market_cap, 2) }}</td>
                <td>{{ number_format($snapshot->volume, 0) }}</td>
                <td>${{ number_format($snapshot->today_high, 2) }}</td>
                <td>${{ number_format($snapshot->today_low, 2) }}</td>
                <td>
                    <span class="{{ $snapshot->change_color }}">
                        ${{ number_format($snapshot->previous_day_change_price, 2) }}
                    </span>
                </td>
                <td>
                    {{ $snapshot->previous_day_change_percent }}%
                </td>
                <td>{{ number_format($snapshot->outstanding_shares, 0) }}</td>
                <td>
                    <button wire:click="$emitTo('longevity-index.edit-stock-snapshot', 'setStockSnapshot', '{{$snapshot->id}}')" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editStockSnapshotModal">
                        edit
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="modal fade" id="editStockSnapshotModal" tabindex="-1" aria-labelledby="editStockSnapshotModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div>
                    <livewire:longevity-index.edit-stock-snapshot />
                </div>
            </div>
        </div>
    </div>
</div>
