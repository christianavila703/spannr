<div class="px-2 pb-2">
    @if($listing->emails !== NULL)
        <ul class="list-group">
            @forelse($listing->emails as $key => $email)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="me-2">{{ $email }}</div>
                    <div class="ms-auto">
                        <button wire:click="removeEmail('{{ $key }}')" class="btn btn-sm text-danger">
                            <i class="fa-sharp fa-solid fa-xmark fa-lg"></i>
                        </button>
                    </div>
                </li>
            @empty
                <li class="list-group-item text-danger fw-bold text-uppercase">
                    You have no emails listed - you won't be notified of anything.
                </li>
            @endforelse
        </ul>
    @endif
    <div class="mt-3">
        <form wire:submit.prevent="submit">
            <div class="d-md-flex">
                <div class="me-3 flex-grow-1">
                    <input wire:model="email" type="text" class="form-control">
                    @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="flex-shrink-0">
                    <button type="submit" class="btn btn-primary text-nowrap">Add Email</button>
                </div>
            </div>
        </form>
    </div>
</div>
