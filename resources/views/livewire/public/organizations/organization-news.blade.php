<div>
    <div id="organization-news" class="organization-related-items-container">
        @foreach ($mediaItems as $mediaItem)
            <div wire:key="news-{{ $mediaItem->id }}" class="organization-related-item mb-3 pb-3">
                <a href="{{ $mediaItem->url }}" class="fw-bold d-block" target="_blank" rel="noopener noreferrer">{{ $mediaItem->name }}</a>
                <span class="text-muted d-block">
                    <span class="me-2">{{ $mediaItem->formatted_date }}</span>
                    <span>{{ $mediaItem->publisher->name }}</span>
                </span>
                <span class="d-block small">
                    {{ $mediaItem->short_summary }}
                </span>
            </div>
        @endforeach
    </div>

    <div>{{ $mediaItems->links() }}</div>

</div>
