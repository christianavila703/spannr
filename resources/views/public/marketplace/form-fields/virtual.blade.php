<label for="virtual" class="d-block fw-bold">Remote / Telehealth Options?</label>
<div class="d-flex align-items-center">
    <div class="form-check form-check-inline">
        <input wire:model="listing.virtual" class="form-check-input" name="virtual" type="radio" id="virtual_yes" value="1">
        <label class="form-check-label" for="virtual_yes">
            Yes
        </label>
    </div>
    <div class="form-check form-check-inline">
        <input wire:model="listing.virtual" class="form-check-input" name="virtual" type="radio" id="virtual_no" value="0">
        <label class="form-check-label" for="virtual_no">
            No
        </label>
    </div>
</div>
@error('listing.virtual') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
