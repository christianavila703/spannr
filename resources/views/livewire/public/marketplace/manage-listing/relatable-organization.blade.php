<div>
    @if($listing->relatable)
        <div class="lead border py-2 px-3">
            <strong>Organization:</strong> <a href="{{ route('public.content.organizations.show', $listing->relatable->slug) }}">{{ $listing->relatable->name }}</a>
            <p class="font-size-sm mb-0 mt-1 text-muted">Connected to the wrong organization? <span wire:click="resetRelatable" class="cursor-pointer fw-bold">Click here to change it</p>
        </div>
    @else
        <div class="mb-2">If your clinic is listed in Spannr, search and connect it below.</div>
        <div class="d-flex max-width-450">
            <div class="me-3 flex-grow-1">
                <input wire:model.lazy="search" type="text" class="form-control" placeholder="Organization name" aria-label="Organization name">
                @error('search') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
            </div>
            <div>
                <button wire:click="search" class="btn btn-primary">Search</button>
            </div>
        </div>
        <ul class="list-group mt-4">
            @forelse($organizations as $organization)
                <li class="list-group-item list-group-item-action">
                    <button wire:click="setRelatable({{ $organization['id'] }})" class="btn w-100 text-start">{{ $organization['name'] }}</button>
                </li>
            @empty
                <li class="list-group-item">No matches found. Email <a href="mailto:help@spannr.com">help@spannr.com</a> to get your organization listed.</li>
            @endforelse
        </ul>
    @endif
</div>
