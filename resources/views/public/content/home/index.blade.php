@extends('layouts.public')

@section('content')
    @include('public.marketplace._hero')
    @include('public.marketplace._nav')
    <div class="container mt-5">
        <div class="max-width-800 mx-auto text-start">
            <p class="marketplace-intro-text max-width-780">Find longevity services and treatments to increase your healthspan and extend your lifespan.</p>
            <div>
                <livewire:public.marketplace.opt-ins.marketplace-search />
            </div>
        </div>
    </div>

    <div id="home-insights" class="article-cards-container container py-6">
        <p class="mb-3 h5 text-quaternary">Most Popular</p>
        <h2 class="mb-0">Our Top Insights:</h2>
        <hr class="dark mb-5">
        <div class="home-articles-container">
            @foreach($articles as $article)
                <article class="home-article" id="article-{{ $article->slug }}">
                    <a href="{{ route('public.content.articles.show', $article->slug) }}">
                        <div class="inside">
                            <div class="featured-image">
                                @if($article->medium_featured_image_url)
                                    <img src="{{ $article->medium_featured_image_url }}" alt="{{ $article->name }}"
                                         class="mb-3 w-100" width="488" height="275">
                                @elseif($article->image)
                                    <img src="{{ $article->image_url }}" alt="{{ $article->name }}">
                                @endif
                            </div>
                            <div class="text">
                                <h3 class="post-title">{{ $article->name }}</h3>
                                <div class="article-excerpt">
                                    {{ $article->short_excerpt }}
                                </div>
                                <div class="article-excerpt post-meta text-uppercase fw-bold d-flex justify-content-between align-items-end">
                                    <div class="mt-3">{{ \Carbon\Carbon::parse($article->date)->format('M j') }}</div>
                                    <div>
                                        @foreach($article->tags as $tag)
                                            @if ($tag->name !== \App\Models\Article::TAG_TOP_INSIGHTS)
                                                <span class="ms-3 mt-3 text-tertiary">{{ $tag->name }}</span>
                                            @endif
                                        @endforeach
                                    </div>

                                </div>
                            </div>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>

        <div class="mt-5 text-center">
            <a href="{{ route('public.content.articles.index') }}" class="btn btn-lg btn-outline-dark">Explore More Insights</a>
        </div>
    </div>

    <div class="magenta-newsletter-container">
        <div class="container">
            <div class="d-lg-flex justify-content-center">
                <div class="max-width-540 me-lg-5 mb-4 mb-lg-0">
                    <h2 class="h1">Live healthier, <span>longer.</span></h2>
                    <p class="lead mb-0">Spannr is the easiest place to access the complex world of longevity. Discover how to increase your healthspan and extend your lifespan.</p>
                </div>
                <div class="mailchimp-form min-width-400 flex-shrink-0 mt-lg-5">
                    <livewire:forms.newsletter-opt-in
                        form="{{ \App\Models\FormEntry::FORM_NEWSLETTER_HOMEPAGE_HERO }}"
                        formClass="email-signup-horizontal-white"
                        successClass="lead text-warning fw-bold"
                    />
                </div>
            </div>
        </div>
    </div>

    <script>
        let marketplaceSearchPresets = document.getElementById('marketplace-search-preset-options');
        let marketplaceSearchBox = document.getElementById('marketplace-search-box');

        marketplaceSearchBox.addEventListener('focusin', function(){
            marketplaceSearchPresets.classList.remove('d-none');
        });

        marketplaceSearchBox.addEventListener('change', function(){
            if (marketplaceSearchBox.value) {
                marketplaceSearchPresets.classList.add('d-none');
            } else {
                marketplaceSearchPresets.classList.remove('d-none');
            }
        });

        window.addEventListener('hide-marketplace-search-presets', event => {
            marketplaceSearchPresets.classList.add('d-none');
        });
    </script>
@endsection
