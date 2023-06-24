<div>
    <div class="d-flex flex-wrap mb-3 mx-2 align-items-center">
        @foreach ($allowedTypes as $allowedType)
            <div class="investor-{{ Str::slug($allowedType) }}">
                <button wire:click="setType('{{ $allowedType }}')" class="btn h4 me-3 @if($allowedType === $type) active @endif">
                    <span>{{ $allowedType }}</span>
                </button>
            </div>
        @endforeach
        <div class="flex-grow-1 d-flex justify-content-end">
            <input wire:model="search" type="text" class="w-auto form-control" placeholder="Search" aria-label="Search companies">
        </div>
    </div>
    <div class="row">
        @forelse ($investors as $investor)
            <div class="col-12 col-md-6 col-lg-4 col-xl-3 company-card">
                <div class="inner-card">
                    <div class="text-center">
                        <div class="logo" style="background-image: url({{ $investor->image_url }});"></div>
                        <h3 class="h5 text-dark mb-4">
                            {{ $investor->name }}
                        </h3>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center text-uppercase text-center">
                        <span class="badge bg-gray-medium text-dark mx-3">{{ $investor->type }}</span>
                        @if($investor->organizations_count > 0)
                            <span class="text-secondary mx-3" data-bs-toggle="tooltip" data-bs-title="Invested in {{ $investor->organizations_count }} companies">
                                <i class="fa-duotone fa-buildings me-2"></i><strong>{{ $investor->organizations_count }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 empty-card">
                <div class="p-5 lead text-secondary fw-bold text-center">No companies match your query</div>
            </div>
        @endforelse
    </div>
    <div>
        {{ $investors->links() }}
    </div>
</div>
