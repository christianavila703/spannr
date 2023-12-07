<div>
    <div wire:poll.visible class="mb-4">
        <ul class="list-group">
            @foreach($listing->people as $record)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <strong>{{ $record->name }}</strong> &ndash; {{ $record->pivot->label }}
                    </div>
                    <button wire:click="removeRelatable('{{ $record->id }}')" aria-label="Remove person" class="btn btn-sm text-danger">
                        <i class="fa-sharp fa-solid fa-xmark fa-lg"></i>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
    @if($person)
        <div class="my-3">
            <form wire:submit.prevent="submit">
                <div class="lead mb-2">
                    Add <strong class="text-success">{{ $person->name }}</strong> to your listing</strong>
                </div>
                <div class="d-flex max-width-450">
                    <div class="me-3 flex-grow-1">
                        <input wire:model="label" type="text" class="form-control" placeholder="Label/Role e.g. Doctor of Specialty" aria-label="Label/Role">
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
            <p class="lead text-muted mb-2">Connect people to your listing</p>
            <div class="d-flex max-width-450">
                <div class="me-3 flex-grow-1">
                    <input wire:model.lazy="search" type="text" class="form-control" placeholder="Name or Email" aria-label="Name or Email">
                    @error('search') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                </div>
                <div>
                    <button wire:click="search" class="btn btn-primary">Search</button>
                </div>
            </div>

            @if($search)
                <ul class="list-group mt-4">
                    @forelse($people as $person)
                        <li class="list-group-item list-group-item-action">
                            <button wire:click="setRelatable({{ $person['id'] }})" class="btn w-100 text-start">{{ $person['name'] }}</button>
                        </li>
                    @empty
                        <li class="list-group-item">No matches found. Email <a href="mailto:help@spannr.com">help@spannr.com</a> to get your person listed.</li>
                    @endforelse
                </ul>
            @endif
        </div>
    @endif
</div>
