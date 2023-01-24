<article id="article-{{ $article->slug }}">
    <a href="{{ route('public.content.articles.show', $article->slug) }}">
        <div>
            <div class="featured-image">
                @if($article->medium_featured_image_url)
                    <img src="{{ $article->medium_featured_image_url }}" alt="{{ $article->name }}" class="mb-3 w-100" width="488" height="275">
                @elseif($article->image)
                    <img src="{{ $article->image_url }}" alt="{{ $article->name }}">
                @endif
            </div>
            <h3 class="post-title">{{ $article->name }}</h3>
            <div class="article-excerpt">
                @if($shortExcerpt === true)
                    {{ $article->short_excerpt }}
                @else
                    {{ $article->excerpt }}
                @endif
            </div>
        </div>
        <div class="d-flex flex-grow-0">
            <div class="read-more">
                <span>Read More</span>
            </div>
        </div>
    </a>
</article>
