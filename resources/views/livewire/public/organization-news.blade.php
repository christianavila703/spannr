<div>
    <div id="organization-news" class="organization-news-container">
        @foreach ($mediaItems as $mediaItem)
            <div class="organization-news-item mb-3 pb-3">
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

    @push('end')
        <script>
            let scrollTop = document.getElementById('organization-news').offsetTop - 50;

            Livewire.on('gotoTop', () => {
                window.scrollTo({
                    top: scrollTop,
                    left: 15,
                    behaviour: 'smooth'
                })
            })
        </script>
    @endpush

</div>
