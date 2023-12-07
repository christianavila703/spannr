<div>
    <div wire:poll.visible class="mb-4">
        <ul class="list-group">
            @foreach($listing->connectedListings as $record)
                <li wire:key="connected-{{ $record->slug }}" class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <strong>{{ $record->name }}</strong> &ndash; {{ $record->pivot->label }}
                    </div>
                    <button wire:click="removeRelatable('{{ $record->id }}', '{{ preg_replace('/[^A-Za-z0-9 ]/', '', $record->name) }}')" aria-label="Remove listing" class="btn btn-sm text-danger">
                        <i class="fa-sharp fa-solid fa-xmark fa-lg"></i>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
    @if($connectedListing)
        <div class="my-3">
            <form wire:submit.prevent="submit">
                <div class="lead mb-2">
                    Add <strong class="text-success">{{ $connectedListing->name }}</strong> to your listing</strong>
                </div>
                <div class="d-flex max-width-450">
                    <div class="me-3 flex-grow-1">
                        <input wire:model="label" type="text" class="form-control" placeholder="Label/Description" aria-label="Label/Description">
                        @error('label') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    @else
        <div>
            <p class="lead text-muted mb-2">Connect other marketplace listings to yours</p>
            <div class="d-flex max-width-450">
                <div class="me-3 flex-grow-1">
                    <input wire:model.lazy="search" type="text" class="form-control" placeholder="Listing Name" aria-label="Listing Name">
                    @error('search') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                </div>
                <div>
                    <button wire:click="search" class="btn btn-primary">Search</button>
                </div>
            </div>

            @if($search)
                <ul class="list-group mt-4">
                    @forelse($connectedListings as $connectedListing)
                        <li class="list-group-item list-group-item-action">
                            <button wire:click="setRelatable({{ $connectedListing['id'] }})" class="btn w-100 text-start">{{ $connectedListing['name'] }}</button>
                        </li>
                    @empty
                        <li class="list-group-item">No matches found. <a href="{{ route('public.marketplace.join') }}">Add your listing</a></li>
                    @endforelse
                </ul>
            @endif
        </div>
    @endif
</div>
