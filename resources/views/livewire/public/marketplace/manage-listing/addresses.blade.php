<div class="px-2">
    <div wire:poll.visible class="mb-3">
        <ul class="list-group">
            @foreach($listing->addresses as $address)
                <li wire:key="address-{{ $address->id }}" class="list-group-item list-group-item-action d-flex align-items-center">
                    <div>
                        @if($address->primary)
                            <span class="btn me-2">
                                <i class="fa-sharp fa-solid fa-star text-primary"></i>
                            </span>
                        @else
                            <button wire:click="setFavorite({{ $address->id }})" aria-label="Set primary location" class="btn me-2">
                                <i class="fa-sharp fa-solid fa-star text-gray-medium"></i>
                            </button>
                        @endif
                    </div>
                    <div class="me-1">{{ $address->name }}</div>
                    <div class="ms-auto">
                        <button wire:click="removeLocation('{{ $address->id }}')" aria-label="Remove address" class="btn btn-sm text-danger"
                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete address">
                            <i class="fa-sharp fa-solid fa-xmark fa-lg"></i>
                        </button>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="mt-2 fw-bold">
        Search and add your location(s) below
    </div>
    <div class="text-secondary font-size-sm mt-1 mb-2">
        Address auto-completion powered by <a href="https://www.geoapify.com/">Geoapify</a>
    </div>
    <div class="d-md-flex max-width-450 mb-3">
        <div class="me-3 flex-grow-1">
            <input wire:model="locationSearch" type="text" class="form-control" aria-label="Search address" placeholder="Search address">
            @if($locationSearchError)
                <div class="text-danger small">{{ $locationSearchError }}</div>
            @endif
        </div>
        <div>
            <button wire:click="searchLocation" class="btn btn-primary">Search</button>
        </div>
    </div>
    <div>
        @if($showResults)
            <ul class="list-group">
                @forelse($results as $resultId => $result)
                    <li class="list-group-item list-group-item-action">
                        <button wire:click="selectLocation('{{ $resultId }}')" class="btn checkbox-choice-button text-start">{{ $result['properties']['formatted'] }}</button>
                    </li>
                @empty
                    <li class="list-group-item">
                        <p class="mb-2">We weren't able to find a match. Try your search again or email <a href="mailto:help@spannr.com">help@spannr.com</a>.</p>
                        <p class="small mb-0">You can also fill out your address below, but all fields are required for geolocation / our map.</p>
                    </li>
                @endforelse
            </ul>
        @endif
    </div>
    <div>
        @if($showForm)
            <form wire:submit.prevent="submit" class="my-3">
                <div class="d-lg-flex">
                    <div class="mb-3 me-lg-3 flex-grow-1">
                        <label for="street" class="small text-secondary">Street Address</label>
                        <input wire:model="street" type="text" class="form-control" id="street">
                        @error('street') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="street2" class="small text-secondary">Street Line 2</label>
                        <input wire:model="street2" type="text" class="form-control" id="street2">
                        @error('street2') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="d-lg-flex">
                    <div class="mb-3 me-lg-3 flex-grow-1">
                        <label for="city" class="small text-secondary">City</label>
                        <input wire:model="city" type="text" class="form-control" id="city">
                        @error('city') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3 me-lg-3">
                        <label for="state" class="small text-secondary">State</label>
                        <input wire:model="state" type="text" class="form-control" id="state">
                        @error('state') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="postal_code" class="small text-secondary">Zip Code</label>
                        <input wire:model="postal_code" type="text" class="form-control" id="postal_code">
                        @error('postal_code') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="d-lg-flex">
                    <div class="mb-3 me-lg-3">
                        <label for="state_code" class="small text-secondary">State Code</label>
                        <input wire:model="state_code" type="text" class="form-control" id="state_code">
                        @error('state_code') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3 me-lg-3">
                        <label for="country" class="small text-secondary">Country</label>
                        <input wire:model="country" type="text" class="form-control" id="country">
                        @error('country') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3 me-lg-3">
                        <label for="country_code" class="small text-secondary">Country Code</label>
                        <input wire:model="country_code" type="text" class="form-control" id="country_code">
                        @error('country_code') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3 me-lg-3">
                        <label for="latitude" class="small text-secondary">Latitude</label>
                        <input wire:model="latitude" type="text" class="form-control" id="latitude">
                        @error('latitude') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3 mb-lg-0">
                        <label for="longitude" class="small text-secondary">Longitude</label>
                        <input wire:model="longitude" type="text" class="form-control" id="longitude">
                        @error('longitude') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        @endif
    </div>
</div>
