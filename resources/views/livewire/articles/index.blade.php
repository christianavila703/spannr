<div>
    @forelse ($articles as $article)
        <article class="single-post archive">
            <a href="{{ route('public.content.articles.show', $article->slug) }}" class="post-title {{ ($article->image) ? 'has-image' : '' }}">
                <div class="overlay">
                    <h2>{{ $article->name }}</h2>
                </div>
                @if($article->image)
                    <img src="{{ $article->image_url }}" alt="{{ $article->name }}">
                @endif
            </a>
            <div class="single-post-content">
                {{ $article->excerpt }}
            </div>
        </article>
    @empty
        <div>
            No articles matched your query
        </div>

    @endforelse

    <div class="text-center">
        {{ $articles->links() }}
    </div>

    @push('end')
        <script>
            Livewire.on('gotoTop', () => {
                window.scrollTo({
                    top: 15,
                    left: 15,
                    behaviour: 'smooth'
                })
            })
        </script>
    @endpush
</div>
