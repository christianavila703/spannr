<div>
    <h2 class="h4 fs-2 mb-3">Set Featured Image</h2>

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
