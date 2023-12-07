<div>
    @if($success)
        <div class="text-success">{{ $success }}</div>
    @else
        <form wire:submit.prevent="submit">
            <div class="d-md-flex justify-content-center">
                <div class="mb-3 mb-md-0 me-md-3 flex-grow-1">
                    <input wire:model="email" type="email" class="form-control" id="email" aria-label="Email" placeholder="Email">
                    @error('email') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3 mb-md-0 me-md-3 flex-grow-1">
                    <input wire:model="password" type="password" class="form-control" id="password" aria-label="Password" placeholder="Password">
                    @error('password') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </div>
        </form>
        @if($error)
            <div class="text-danger">{{ $error }}</div>
        @endif
    @endif
</div>
