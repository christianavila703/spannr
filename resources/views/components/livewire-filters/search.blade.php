<div>
    <div class="d-flex align-items-center">
        <input wire:model.lazy="search" type="text" class="form-control me-2" placeholder="{{ $placeholder }}" aria-label="{{ $label }}">
        <span>
            <i class="fa-sharp fa-solid fa-circle-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $tooltip }}"></i>
        </span>
    </div>

    <div class="small mt-2">
        @if($search)
            <span class="me-1">Searching for:</span>
            <strong>{{ $search }}</strong>
            <button wire:click="clearSearch" class="btn text-danger px-1 border-0" aria-label="Clear search"><i class="fa-sharp fa-solid fa-circle-xmark"></i></button>
        @endif
    </div>
</div>
