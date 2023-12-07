<div class="max-width-600 mx-auto text-start">
    <div class="h6 mb-2">Your Info</div>
    @if($loggedIn)
        <div class="text-secondary mb-4">Welcome back, {{ $user->name }}! We have your details.</div>
        @if($success)
            <div class="my-4 text-success">{{ $success }}</div>
        @endif
        <form wire:submit.prevent="submit">
            <div class="mb-4 d-md-flex justify-content-center align-items-center">
                <label class="h6 text-nowrap me-md-4 mb-md-0" for="type">Listing Type</label>
                <select wire:model="type" id="type" class="form-select">
                    <option value="">--</option>
                    @foreach($typeOptions as $typeOption)
                        <option value="{{ $typeOption }}">{{ $typeOption }}</option>
                    @endforeach
                </select>
            </div>
            @if($type)
                <div>
                    <div class="h6">Details</div>
                    <div class="mb-3">
                        <label for="name" class="small text-secondary">{{ $type }} Name</label>
                        <input type="text" wire:model.lazy="name" class="form-control @error('name') is-invalid @enderror">
                        @error('name') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="small text-secondary">Email (for notifications)</label>
                        <input type="text" wire:model.lazy="email" class="form-control @error('email') is-invalid @enderror" id="email">
                        @error('email') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="website" class="small text-secondary">Website</label>
                            <input type="url" wire:model.lazy="website" class="form-control @error('website') is-invalid @enderror" aria-label="Website" placeholder="https://website.com">
                            @error('website') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="phone" class="small text-secondary">Phone</label>
                            <input type="tel" wire:model.lazy="phone" class="form-control @error('phone') is-invalid @enderror" aria-label="Telephone" placeholder="555-555-5555">
                            @error('phone') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="summary" class="small text-secondary">Summary / Short Description</label>
                        <textarea wire:model.lazy="summary" id="summary" rows="6" class="form-control @error('summary') is-invalid @enderror" placeholder="Up to 250 characters - you'll be able to add a long description later"></textarea>
                        <div class="small text-secondary">
                            <span class="{{ $summaryColor }}">{{ $summaryCharacters }}</span>
                            <span>characters</span>
                        </div>
                        @error('summary') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Next</button>
                </div>
            @endif
        </form>
    @else
        <div>
            @if($showRegisterForm)
                <p class="mb-1">
                    You'll need a Spannr account so let's create one.
                    <span wire:click="toggleRegisterForm" class="mb-1 text-tertiary font-size-sm cursor-pointer">
                        Have an account? Login
                    </span>
                </p>
                <livewire:auth.register />
            @else
                <p class="mb-1">
                    Login to your Spannr account.
                    <span wire:click="toggleRegisterForm" class="mb-1 text-tertiary font-size-sm cursor-pointer">
                        Need an account? Create one
                    </span>
                </p>
                <livewire:auth.login />
            @endif
        </div>
    @endif
</div>
