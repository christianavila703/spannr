<div class="media-listing-container row">
    @foreach ($images as $image)
        <div wire:key="image-{{ $image->id }}" class="col-12 col-xl-6 mb-4">
            <div class="media-listing-item card card-body">
                <div class="d-flex">
                    <div class="flex-shrink-0 pe-3">
                        <img src="{{ $image->getUrl('preview') }}" alt="" width="150" height="150">
                    </div>
                    <div class="text-content flex-grow-1">
                        <div class="pb-3">
                            <p wire:poll class="h6 fs-6">{{ $image->name }}</p>
                            <input type="text" class="form-control form-control-sm" value="{{ $image->getUrl() }}" aria-label="URL" disabled>
                        </div>
                        <div class="d-flex align-items-start">
                            <button wire:click="deleteConfirmation({{$image->id}})" class="btn btn-btn-sm btn-link text-danger me-3 text-nowrap">
                                <i class="fa-sharp fa-solid fa-trash me-1"></i>delete
                            </button>
                            <div class="d-flex flex-wrap">
                                @if($deletableImage === $image->id)
                                    <span class="delete-warning me-2">
                                        are you sure?
                                    </span>
                                    <button wire:click="deleteItem({{$image->id}})" class="btn btn-btn-sm btn-danger me-2">YES DELETE!</button>
                                    <button wire:click="resetDeletable" class="btn btn-sm btn-link">nevermind</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
