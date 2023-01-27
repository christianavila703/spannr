<div>
    <h2 class="h4 me-2">Add Post Image</h2>

    <form method="POST" wire:submit.prevent="submit">

        <div class="mb-3 d-lg-flex align-items-center flex-wrap">
            <input type="file" id="image" wire:model.debounce.500ms="image" class="form-control max-width-500 me-3">
            @error('image') <div class="text-danger fw-bold">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3 d-lg-flex align-items-center">
            <input type="text" id="description" wire:model="description" class="form-control max-width-500 me-3" aria-label="Alt Description" placeholder="Alt Description">
            @error('description') <div class="text-danger fw-bold">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary">Upload</button>
            <div class="text-success fw-bold px-2">
                {{ $message }}
            </div>
        </div>
    </form>
</div>
