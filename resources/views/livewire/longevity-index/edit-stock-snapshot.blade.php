<div>
    @if ($snapshot)
        <form wire:submit.prevent="save">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="editStockSnapshotModalLabel">Edit {{ $snapshot->stock->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="market_cap" class="fw-bold">Market Cap</label>
                    <input type="text" wire:model="snapshot.market_cap" class="form-control" id="market_cap">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Save Changes</button>
            </div>
        </form>
    @endif
</div>
