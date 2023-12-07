<input type="tel" wire:model.lazy="listing.phone" class="form-control @error('phone') is-invalid @enderror" aria-label="Telephone" placeholder="555-555-5555">
@error('listing.phone') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
