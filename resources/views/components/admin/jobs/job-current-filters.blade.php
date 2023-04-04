<div class="current-filters d-flex align-items-center flex-wrap">
    @if($filters['category'])
    <div class="current-filter-value-group full-size">
        @foreach($filters['category'] as $category)
            @if($category !== false)
            <div class="current-filter-value">
                <span>{{ $category }}</span>
                <button wire:click="removeFilterArrayItem('category', '{{ $category }}')" class="btn btn-sm btn-link text-muted me-3">
                    <i class="fa-sharp fa-regular fa-xmark fa-lg"></i>
                </button>
            </div>
            @endif
        @endforeach
    </div>
    @endif

    @if($search)
    <div class="current-filter-value">
        <span>{{ $search }}</span>
        <button wire:click="resetSearch" class="btn btn-sm btn-link text-muted me-3">
            <i class="fa-sharp fa-regular fa-xmark fa-lg"></i>
        </button>
    </div>
    @endif

    @if($filters['type'])
    <div class="current-filter-value-group">
        @foreach($filters['type'] as $type)
            @if($type !== false)
            <div class="current-filter-value">
                <span>{{ $type }}</span>
                <button wire:click="removeFilterArrayItem('type', '{{ $type }}')" class="btn btn-sm btn-link text-muted me-3">
                    <i class="fa-sharp fa-regular fa-xmark fa-lg"></i>
                </button>
            </div>
            @endif
        @endforeach
    </div>
    @endif

    @if($filters['remote'])
    <div class="current-filter-value">
        <span class="text-primary">Remote</span>
        <button wire:click="resetFilter('remote')" class="btn btn-sm btn-link text-muted me-3">
            <i class="fa-sharp fa-regular fa-xmark fa-lg"></i>
        </button>
    </div>
    @endif
</div>

<div class="d-md-flex justify-content-between align-items-center">
    @if($search OR $filters['type'] OR $filters['category'] OR $filters['remote'])
        <p class="text-muted mb-0 mb-3">{{ $records->total() }} results</p>
        <button wire:click="resetFilters" class="btn btn-sm mb-3 btn-dark">Clear Filters</button>
    @endif
</div>
