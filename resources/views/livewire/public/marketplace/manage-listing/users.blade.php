<div>
    <ul class="list-group">
        @foreach($listing->users as $user)
            <li wire:key="user-{{ $user->id }}" class="list-group-item list-group-item-action d-flex align-items-center">
                <span style="width: 120px">
                    @if($user->pivot->role === \App\Models\Listing::USER_ROLE_OWNER)
                        <span class="me-2">
                            <i class="fa-sharp fa-solid fa-star fa-fw text-primary"></i>
                        </span>
                    @else
                        <span class="me-2">
                            <i class="fa-sharp fa-solid fa-user fa-fw text-muted"></i>
                        </span>
                    @endif
                    <span>{{ $user->pivot->role }}</span>
                </span>
                <strong class="me-2">{{ $user->full_name }}</strong>
                <span class="me-2 fst-italic">({{ $user->email }})</span>
                <div class="ms-auto">
                    <button type="button" wire:click="removeUser('{{ $user->id }}')" aria-label="Remove user" class="btn btn-sm text-danger">
                        <i class="fa-sharp fa-solid fa-xmark fa-lg"></i>
                    </button>
                </div>
            </li>
        @endforeach
    </ul>
    <p class="font-size-sm text-muted mt-1">Managers and Owners can update the listing. Owners can add/remove users.</p>
    <form wire:submit.prevent="submit" class="mt-4">
        <p class="mb-2 fw-bold">Add a user to own / manage this listing</p>
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <input wire:model="email" type="text" class="form-control" aria-label="Email" placeholder="Email">
                @error('email') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-6 mb-3">
                <select wire:model="role" class="form-select" aria-label="Role">
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}">{{ $role }}</option>
                    @endforeach
                </select>
                @error('role') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
        </div>
        <div>
            @if($error)
                <div class="mb-3 text-danger">{{ $error }}</div>
            @endif
        </div>
        <div>
            @if($showRegistrationForm)
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <input wire:model="first_name" type="text" class="form-control" id="first_name" aria-label="First name" placeholder="First name">
                        @error('first_name') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <input wire:model="last_name" type="text" class="form-control" id="last_name" aria-label="Last name" placeholder="Last name">
                        @error('last_name') <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                    </div>
                </div>
            @endif
        </div>
        <div class="mb-3">
            <div class="form-check">
                <input wire:model="send_email" class="form-check-input" type="checkbox" id="send_email">
                <label class="form-check-label" for="send_email">
                    Send email notification
                </label>
            </div>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">Invite User</button>
        </div>
    </form>
    <div>
        @if($success)
            <div class="my-3 text-success">{{ $success }}</div>
        @endif
    </div>
</div>
