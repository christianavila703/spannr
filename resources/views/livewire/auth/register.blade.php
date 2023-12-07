<div>
    @if($success)
        <div class="text-success">{{ $success }}</div>
    @else
        <form wire:submit.prevent="submit">
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <input wire:model="name" type="text" class="form-control" id="name" aria-label="First name" placeholder="First name">
                    @error('name') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <input wire:model="last_name" type="text" class="form-control" id="last_name" aria-label="Last name" placeholder="Last name">
                    @error('last_name') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <input wire:model="email" type="email" class="form-control" id="email" aria-label="Email" placeholder="Email">
                    @error('email') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <input wire:model="password" type="password" class="form-control me-1" id="password" aria-label="Password" placeholder="Password">
                    @error('password') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Register</button>
                </div>
            </div>
        </form>
        @if($error)
            <div class="text-danger">{{ $error }}</div>
        @endif
    @endif
</div>
