<div>
    <form wire:submit.prevent="submit">
        <div class="d-flex max-width-450">
            <div class="me-3 flex-grow-1">
                <input type="text" wire:model="license" class="form-control @error('license') is-invalid @enderror" aria-label="Add a license" placeholder="Add a license">
                @error('license') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </form>
    <ul class="list-group mt-3 ps-0">
        @foreach($current as $key => $license)
            <div wire:key="license-{{ $key }}" wire:poll.visible class="list-group-item d-flex justify-content-between align-items-center">
                <span class="me-2">{{ $license }}</span>
                <button wire:click="removeOption('{{ $key }}')" aria-label="Remove option" class="btn btn-sm text-danger">
                    <i class="fa-sharp fa-solid fa-xmark fa-lg"></i>
                </button>
            </div>
        @endforeach
    </ul>
</div>
