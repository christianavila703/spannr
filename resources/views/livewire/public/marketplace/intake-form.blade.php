<div class="max-width-600 mx-auto text-start">
    <div class="h6 mb-2">Your Info</div>
    @if($loggedIn)
        <div class="text-secondary mb-4">Welcome back, {{ $user->name }}! We have your details.</div>
        @if($success)
            <div class="my-4 text-success">{{ $success }}</div>
        @endif
        @include('public.marketplace.forms.create-listing-form')
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
