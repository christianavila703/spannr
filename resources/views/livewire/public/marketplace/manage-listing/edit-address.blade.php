<div>
    <form wire:submit.prevent="submit" class="my-3 border p-3">
        <div class="d-lg-flex">
            <div class="mb-3 me-lg-3 flex-grow-1">
                <label for="street" class="small text-secondary">Street Address</label>
                <input wire:model="address.street" type="text" class="form-control" id="street">
                @error('street') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="street2" class="small text-secondary">Street Line 2</label>
                <input wire:model="address.street2" type="text" class="form-control" id="street2">
                @error('street2') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="d-lg-flex">
            <div class="mb-3 me-lg-3 flex-grow-1">
                <label for="city" class="small text-secondary">City</label>
                <input wire:model="address.city" type="text" class="form-control" id="city">
                @error('city') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3 me-lg-3">
                <label for="state" class="small text-secondary">State</label>
                <input wire:model="address.state" type="text" class="form-control" id="state">
                @error('state') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="postal_code" class="small text-secondary">Zip Code</label>
                <input wire:model="address.postal_code" type="text" class="form-control" id="postal_code">
                @error('postal_code') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="d-lg-flex">
            <div class="mb-3 me-lg-3">
                <label for="state_code" class="small text-secondary">State Code</label>
                <input wire:model="address.state_code" type="text" class="form-control" id="state_code">
                @error('state_code') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3 me-lg-3">
                <label for="country" class="small text-secondary">Country</label>
                <input wire:model="address.country" type="text" class="form-control" id="country">
                @error('country') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3 me-lg-3">
                <label for="country_code" class="small text-secondary">Country Code</label>
                <input wire:model="address.country_code" type="text" class="form-control" id="country_code">
                @error('country_code') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3 me-lg-3">
                <label for="latitude" class="small text-secondary">Latitude</label>
                <input wire:model="address.latitude" type="text" class="form-control" id="latitude">
                @error('latitude') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3 mb-lg-0">
                <label for="longitude" class="small text-secondary">Longitude</label>
                <input wire:model="address.longitude" type="text" class="form-control" id="longitude">
                @error('longitude') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="mt-1">
            <div class="form-check">
                <input wire:model="address.primary" class="form-check-input" type="checkbox" value="" id="primary">
                <label class="form-check-label" for="primary">
                    Primary address
                </label>
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>
