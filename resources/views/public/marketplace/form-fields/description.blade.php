<label for="description" class="d-block fw-bold">Description</label>
@error('listing.description') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
<div wire:ignore>
    <div class="pseudo-textarea">
        <textarea wire:model="listing.description" id="description" wire:ignore></textarea>
    </div>
    <div class="tiny-editor">
        <textarea id="listing-description" name="listing-description" aria-label="Listing Description">{{ $listing->description }}</textarea>
    </div>
</div>
