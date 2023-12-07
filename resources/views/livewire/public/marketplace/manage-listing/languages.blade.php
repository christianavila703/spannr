<div class="row">
    <div class="col-12 col-md-6 d-flex flex-wrap">
        <div class="mb-2 ms-xl-3 d-flex align-items-center">
            <input wire:model.lazy="search" type="text" class="form-control form-control-sm w-auto" aria-label="Search" placeholder="Search">
            <div>
                @if($search) <button wire:click="clearSearch" class="btn btn-sm text-danger"><i class="fa-sharp fa-solid fa-xmark"></i></button> @endif
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <button wire:click="toggleCreateForm" class="btn btn-outline-quaternary btn-sm ms-xl-3">Add new language</button>
    </div>
    <div class="col-12">
        @if($createNew)
            <div class="border border-2 border-primary p-2 ms-xl-3 max-width-400 my-2 d-md-flex">
                <input wire:model="newItem" type="text" class="form-control form-control-sm me-2" aria-label="Language" placeholder="Language">
                <button wire:click="createItem" class="btn btn-sm btn-primary">Save</button>
            </div>
        @endif
    </div>
    @forelse($options as $languageId => $language)
        <div wire:poll.visible wire:key="language-option-{{ $languageId }}" class="col-12 col-md-6">
            @if(in_array($language, $current))
                <button wire:click="removeOption('{{ $languageId }}', '{{ $language }}')" aria-label="Remove language" class="btn checkbox-choice-button text-success">
                    <i class="fa-sharp fa-solid fa-check fa-lg"></i>
                    <span>{{ $language }}</span>
                </button>
            @else
                <button wire:click="addOption('{{ $languageId }}', '{{ $language }}')" aria-label="Add language" class="btn checkbox-choice-button text-secondary">
                    <i class="fa-sharp fa-solid fa-plus fa-lg"></i>
                    <span>{{ $language }}</span>
                </button>
            @endif
        </div>
    @empty
        <div wire:key="empty-no-records">
            <div class="ps-xl-3 pt-2">
                No languages match your search
            </div>
        </div>
    @endforelse
</div>
