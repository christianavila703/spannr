<form wire:submit.prevent="submit">
    <div class="mb-4 d-md-flex justify-content-center align-items-center">
        <label class="h6 text-nowrap me-md-4 mb-md-0" for="type">Listing Type</label>
        <select wire:model="type" id="type" class="form-select">
            <option value="">--</option>
            @foreach($typeOptions as $typeOption)
                <option value="{{ $typeOption }}">{{ $typeOption }}</option>
            @endforeach
        </select>
    </div>
    @if($type)
        <div>
            <div class="h6">Details</div>
            <div class="mb-3">
                <label for="name" class="small text-secondary">{{ $type }} Name</label>
                <input type="text" wire:model.lazy="name" class="form-control @error('name') is-invalid @enderror">
                @error('name') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="email" class="small text-secondary">Email (for notifications)</label>
                <input type="text" wire:model.lazy="email" class="form-control @error('email') is-invalid @enderror" id="email">
                @error('email') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
            </div>
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="website" class="small text-secondary">Website</label>
                    <input type="url" wire:model.lazy="website" class="form-control @error('website') is-invalid @enderror" aria-label="Website" placeholder="https://website.com">
                    @error('website') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="phone" class="small text-secondary">Phone</label>
                    <input type="tel" wire:model.lazy="phone" class="form-control @error('phone') is-invalid @enderror" aria-label="Telephone" placeholder="555-555-5555">
                    @error('phone') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="summary" class="small text-secondary">Summary / Short Description</label>
                <textarea wire:model.lazy="summary" id="summary" rows="6" class="form-control @error('summary') is-invalid @enderror" placeholder="Up to 250 characters - you'll be able to add a long description later"></textarea>
                <div class="small text-secondary">
                    <span class="{{ $summaryColor }}">{{ $summaryCharacters }}</span>
                    <span>characters</span>
                </div>
                @error('summary') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn btn-primary">Next</button>
        </div>
    @endif
</form>
