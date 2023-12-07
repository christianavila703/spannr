<div>
    <h2 class="{{ $headingClass }}">Set Featured Image</h2>
    @if($imageRequirements)
        <p class="text-muted mb-2">{{ $imageRequirements }}</p>
    @endif

    <form method="POST" wire:submit.prevent="submit">

        <div class="mb-3">
            <input type="file" id="featuredImage" wire:model.debounce.500ms="featuredImage" class="form-control">
        </div>

        <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary">Save</button>
            <div class="text-success fw-bold px-2">
                {{ $message }}
            </div>
            @error('featuredImage') <div class="text-danger fw-bold">{{ $message }}</div> @enderror
        </div>
    </form>
</div>
