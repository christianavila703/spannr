<label for="insurance" class="d-block fw-bold">Insurance Accepted?</label>
<div class="d-flex align-items-center">
    <div class="form-check form-check-inline">
        <input wire:model="listing.insurance" class="form-check-input" name="insurance" type="radio" id="insurance_yes" value="1">
        <label class="form-check-label" for="insurance_yes">
            Yes
        </label>
    </div>
    <div class="form-check form-check-inline">
        <input wire:model="listing.insurance" class="form-check-input" name="insurance" type="radio" id="insurance_no" value="0">
        <label class="form-check-label" for="insurance_no">
            No
        </label>
    </div>
</div>
@error('listing.insurance') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
