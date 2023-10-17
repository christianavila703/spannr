<div class="spannr-plus-signup-form-container">
    @if($error)
        <div class="fs-5 text-white">
            Sorry there was a problem. <a href="{{ route('login') }}" class="text-white fw-bold">Login here</a>
        </div>
    @endif
    @if($success)
        <div class="fs-4 text-white">
            <p class="mb-3">Your Spannr account was created!</p>
            <a href="{{ $subscriptionUrl }}" class="btn btn-tertiary btn-lg">Subscribe to Spannr+</a>
        </div>
    @else
        <form wire:submit.prevent="submit" class="email-signup-horizontal-purple-hover mb-4">
            <div class="form-group-container">
                <div class="email-field mb-3">
                    <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email" id="email" placeholder="Email" aria-label="Email">
                </div>
                <div class="submit-field">
                    <button type="submit" class="btn btn-outline-primary">Sign Up</button>
                </div>
            </div>
            <div class="spannr-plus-signup-success-label">
                <span class="d-block form-error text-danger">
                    @error('email') <span class="me-1">{{ $message }}</span> @enderror
                    <span>Existing user? <a href="{{ route('login') }}">Login here</a></span>
                </span>
            </div>
        </form>
    @endif
</div>
