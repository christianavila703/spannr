<div>
    @forelse ($articles as $article)
        <article class="single-post archive">
            <div class="image-container">
                <a href="{{ route('public.content.articles.show', $article->slug) }}">
                    @if($article->featured_image_url)
                        <img src="{{ $article->featured_image_url }}" alt="{{ $article->name }}" class="mb-3 w-100" width="1600" height="900">
                    @elseif($article->image)
                        <img src="{{ $article->image_url }}" alt="{{ $article->name }}">
                    @endif
                </a>
            </div>
            <div class="text-container">
                <div class="max-width-540 mx-auto">
                    <a href="{{ route('public.content.articles.show', $article->slug) }}" class="post-title text-black">
                        <h2>{{ $article->name }}</h2>
                    </a>
                    <div class="single-post-content archive-excerpt">
                        {{ $article->excerpt }}
                    </div>
                    <div class="link">
                        <a href="{{ route('public.content.articles.show', $article->slug) }}" class="link-black-underline-gradient d-inline-block text-black"><span>Read More</span></a>
                    </div>
                </div>
            </div>
        </article>
    @empty
        <div>
            No articles matched your query
        </div>

    @endforelse

    <div class="text-center mt-3 mb-5">
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
