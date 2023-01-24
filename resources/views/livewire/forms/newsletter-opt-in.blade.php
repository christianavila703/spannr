<div class="livewire-newsletter-optin-component">
    <form wire:submit.prevent="submit" class="{{ $formClass }}">
        @csrf
        <div>
            @if ($showSuccess === true)
                <div>
                    <p class="{{ $successClass }}">Thanks for subscribing!</p>
                </div>
            @else
                <div class="form-group-container">
                    @if($showNameFields)
                        <div class="name-fields">
                            <div class="mb-3">
                                <input type="text" class="form-control" wire:model="first_name" id="first_name" placeholder="First name" aria-label="First name">
                                @error('first_name') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" wire:model="last_name" id="last_name" placeholder="Last name" aria-label="Last name">
                                @error('last_name') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif
                    <div class="email-field mb-3">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email" id="email" placeholder="Email" aria-label="Email">
                        @error('email') <span class="form-error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="submit-field">
                        <input type="hidden" name="url" value="{{ $url }}">
                        <input type="hidden" name="ip" value="{{ $ip }}">
                        <button type="submit" class="btn btn-outline-primary">Sign Up</button>
                    </div>
                </div>
            @endif
        </div>
    </form>
</div>
