<div class="marketplace-location-search-container">
    <form wire:submit.prevent="submit">
        <div class="d-md-flex align-items-center">
            <input wire:model="zip_code" type="text"
                   class="form-control form-control-lg me-3 mb-3 mb-md-0 max-width-200 @error('zip_code') is-invalid @enderror"
                   placeholder="Zip code"
                   aria-label="Zip code">
            <div class="input-group me-3 mb-3 mb-md-0 max-width-200">
                <input wire:model="distance" type="number"
                       class="form-control form-control-lg @error('distance') is-invalid @enderror"
                       placeholder="100" step="1"  aria-label="Distance in miles">
                <span class="input-group-text">miles</span>
            </div>
            <div>
                <button type="submit" class="btn btn-primary btn-lg">Search</button>
            </div>
        </div>
        <div>
            @error('zip_code')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
            @error('distance')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
            @if('error')
                <span class="text-danger small">{{ $error }}</span>
            @endif
        </div>
    </form>
</div>
