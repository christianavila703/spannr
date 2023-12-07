<div>
    <form wire:submit.prevent="submit" class="max-width-600">
        <div class="mb-3">
            <input type="text" wire:model.lazy="name" class="form-control @error('name') is-invalid @enderror" aria-label="Name" placeholder="Name">
            @error('name') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <input type="text" wire:model.lazy="description" class="form-control @error('description') is-invalid @enderror" aria-label="Description" placeholder="Description">
            @error('description') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
        </div>
        <div>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
    <ul class="list-group mt-5">
        @foreach($records as $record)
            <a wire:key="record-{{ $record->id }}" href="{{ route('admin.tag-sets.show', $record->id) }}" class="list-group-item list-group-item-action">
                <p class="fw-bold mb-1">{{ $record->name }}</p>
                <p class="mb-0">{{ $record->description }}</p>
            </a>
        @endforeach
    </ul>
</div>
