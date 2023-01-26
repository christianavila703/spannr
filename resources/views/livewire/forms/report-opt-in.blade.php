<div class="livewire-report-optin-component">
    <form wire:submit.prevent="submit">
        @csrf
        <div class="form-wrapper">
            @if ($showSuccess === true)
                <div>
                    <p class="{{ $successClass }} mt-2 fw-bold mb-0">Success! Report is on the way.</p>
                </div>
            @else
                <div class="form-group-container">
                    <div class="email-field">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email" id="email" placeholder="Email" aria-label="Email">
                        @error('email') <span class="form-error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="submit-field">
                        <input type="hidden" name="url" value="{{ $url }}">
                        <input type="hidden" name="ip" value="{{ $ip }}">
                        <button type="submit" class="btn btn-outline-primary">Access Report</button>
                    </div>
                </div>
            @endif
        </div>
    </form>
</div>
