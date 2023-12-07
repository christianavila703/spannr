<textarea wire:model.lazy="listing.summary" id="summary" rows="3" class="form-control @error('summary') is-invalid @enderror" aria-label="Short Description / Summary" placeholder="Short Description / Summary"></textarea>
<small class="text-secondary">Summary is up to 250 characters</small>
@error('listing.summary') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
