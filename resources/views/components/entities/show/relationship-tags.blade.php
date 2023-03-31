@props([
    'entity',
])

<div>
    @forelse ($entity->tags as $item)
        <p class="my-2">
            <a href="{{ route('admin.tags.show', $item->slug) }}">{{ $item->name }}</a>
            <a href="#" class="ms-2 btn btn-sm btn-outline-danger js-detach-tag-btn"
                    data-action-route="{{ route('admin.tags.detach') }}"
                    data-taggable-id="{{ $entity->id }}"
                    data-taggable-type="{{ $entity::class }}"
                    data-tag-id="{{ $item->id }}"><i class="fa fa-trash"></i></a>
        </p>
    @empty
        <p class="my-2 text-muted">&ndash;</p>
    @endforelse
</div>

@push('end')
    <script type="module" src="{{ Vite::asset('resources/js/entity-tags.js') }}"></script>
@endpush
