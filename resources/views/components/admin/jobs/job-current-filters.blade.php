<div class="current-filters d-flex align-items-center flex-wrap">
    <div class="current-filter-value-group full-size">
        @if($filters['category'])
            @foreach($filters['category'] as $id => $category)
                <div class="current-filter-value">
                    @if ($category)
                        <span>{{ $category }}</span>
                        <button wire:click="resetFilterArray('category', '{{ $id }}')" class="btn btn-sm btn-link text-muted me-3">
                            <i class="fa-sharp fa-regular fa-xmark fa-lg"></i>
                        </button>
                        @endif
                </div>
            @endforeach
        @endif
    </div>

    <div class="current-filter-value">
        @if($search)
            <span>{{ $search }}</span>
            <button wire:click="resetSearch" class="btn btn-sm btn-link text-muted me-3">
                <i class="fa-sharp fa-regular fa-xmark fa-lg"></i>
            </button>
        @endif
    </div>

    <div class="current-filter-value-group">
        @if($filters['type'])
            @foreach($filters['type'] as $id => $type)
                <div class="current-filter-value">
                    @if($type)
                        <span>{{ $type }}</span>
                        <button wire:click="resetFilterArray('type', '{{ $id }}')" class="btn btn-sm btn-link text-muted me-3">
                            <i class="fa-sharp fa-regular fa-xmark fa-lg"></i>
                        </button>
                    @endif
                </div>
            @endforeach
        @endif
    </div>

    <div class="current-filter-value">
        @if($filters['remote'])
            <span class="text-primary">Remote</span>
            <button wire:click="resetFilter('remote')" class="btn btn-sm btn-link text-muted me-3">
                <i class="fa-sharp fa-regular fa-xmark fa-lg"></i>
            </button>
        @endif
    </div>
</div>
<div class="d-md-flex justify-content-between align-items-center">
    @if($search OR $filters['type'] OR $filters['category'] OR $filters['remote'])
        <p class="text-muted mb-0 mb-3">{{ $records->total() }} results</p>
        <button wire:click="resetFilters" class="btn btn-sm mb-3 btn-dark">Clear Filters</button>
    @endif
</div>
