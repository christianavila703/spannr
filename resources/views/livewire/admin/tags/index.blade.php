<div>
    <div class="mb-4 d-flex align-items-center">
        <div class="me-4">
            <input wire:model="filters.search" type="text" class="form-control" placeholder="Search...">
        </div>
        <div>
            <button wire:click="resetFilters" class="btn btn-sm">Clear Search</button>
        </div>
        <div class="ms-auto lead">
            @if($filters['search'])
                <span>{{ $tags->total() }} results</span>
            @endif
        </div>
    </div>
    <div>
        @if($filters['search'])
            <ul class="list-group my-4">
                @foreach($tags as $tag)
                    <li class="list-group-item d-flex align-items-center">
                        <a href="{{ route('admin.tags.show', $tag->slug) }}" class="d-block me-4">{{ $tag->name }}</a>

                        <div class="d-flex align-items-center small text-muted">{!! $tag->breadcrumb !!}</div>
                    </li>
                @endforeach
            </ul>
            <div>
                {{ $tags->links() }}
            </div>
        @endif
    </div>
</div>
