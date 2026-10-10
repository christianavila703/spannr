@extends('layouts.public')

@push('seo-extra')
{!! \App\Support\StructuredData::forArticle($article) !!}
<meta name="twitter:creator" content="@spannr_" />
<meta name="twitter:label1" content="Written by" />
<meta name="twitter:data1" content="{{ $article->author_byline }}" />
<meta name="twitter:label2" content="Est. reading time" />
<meta name="twitter:data2" content="{{ $article->reading_time_minutes }} minutes" />
@endpush

@section('content')
    <div class="single-post-wrapper">
        <div class="container">
            <div class="single-post-content-container">
                <x-nav.breadcrumbs parentUrl="{{ route('public.content.articles.index') }}" parentLabel="Insights" currentLabel="{{ $article->name }}"/>
                <hr class="dark mb-5">

                <div class="single-post pb-4">
                  <div class="article-column">
                    <h1 class="post-title mb-4">{{ $article->name }}</h1>
                    @include('public.content.articles._author-box')
                    @if($featuredImage)
                        <div class="mb-3">
                            <picture class="featured-image-picture">
                                <source media="(min-width: 768px) and (max-width: 1023px)" srcset="{{ $article->medium_featured_image_url ?? $featuredImage }}" />
                                <source media="(min-width: 1024px)" srcset="{{ $featuredImage }}"/>
                                <img src="{{ $article->small_featured_image_url ?? $featuredImage }}" alt="{{ $article->name }}" />
                            </picture>
                        </div>
                    @elseif($article->image)
                        <img src="{{ $article->image_full_url }}" alt="{{ $article->name }}" class="mb-3 w-100" width="1616" height="824">
                    @endif
                    <div class="single-post-content-sidebar-container">
                        <article class="single-post-content article-full-width pb-6">
                            <div class="post-meta-container">
                                <div class="author">
                                    @forelse($article->authors as $author)
                                        {{ $loop->first ? 'By' : '' }}
                                         <a href="{{ $author->about_page_url }}">{{ $author->byline ?? $author->name }}</a>
                                        {{ !$loop->last ? '/' : '' }}
                                    @empty
                                        By Spannr
                                    @endforelse

                                    @can('edit articles')
                                        <span class="ms-3">
                                            <a href="{{ route('admin.articles.edit', $article->id) }}" class="text-tertiary">Edit</a>
                                        </span>
                                    @endcan
                                </div>
                                <div class="single-post-share-container">
                                    <a href="{{ $article->shareable_twitter_url }}" class="social-icon" target="_blank" rel="noopener noreferrer"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Share on Twitter">
                                        <i class="fa-brands fa-twitter"></i>
                                    </a>
                                    <a href="{{ $article->shareable_facebook_url }}" class="social-icon" target="_blank" rel="noopener noreferrer"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Share on Facebook">
                                        <i class="fa-brands fa-facebook"></i>
                                    </a>
                                    <a href="{{ $article->shareable_linkedin_url }}" class="social-icon" target="_blank" rel="noopener noreferrer"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Share on LinkedIn">
                                        <i class="fa-brands fa-linkedin-in"></i>
                                    </a>
                                    <a href="{{ $article->shareable_reddit_url }}" class="social-icon" target="_blank" rel="noopener noreferrer"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Share on Reddit">
                                        <i class="fa-brands fa-reddit"></i>
                                    </a>
                                    <a href="{{ $article->shareable_hacker_news_url }}" class="social-icon" target="_blank" rel="noopener noreferrer"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Share on Hacker News">
                                        <i class="fa-brands fa-hacker-news"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="post-content">
                                {!! $article->content !!}

                                @if($article->sources)
                                    <div id="article-citations">
                                        <div>
                                            <a class="h4 citation-title" data-bs-toggle="collapse" data-bs-target="#article-sources"
                                               aria-expanded="false" aria-controls="article-sources" aria-label="Expand sources">
                                                <span class="icon"></span><span>Sources</span>
                                            </a>
                                        </div>
                                        <div class="collapse" id="article-sources">
                                            <div class="fs-6">
                                                {!! $article->sources !!}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if($article->sidebar)
                                {{-- The CMS "Sidebar" field. It used to sit in the right column; the column is gone, so it follows the body. --}}
                                <div class="article-extra mt-5">{!! $article->sidebar !!}</div>
                            @endif
                            @if($article->slug === \App\Support\CityGuideIndex::NATIONAL_SLUG)
                                @include('public.content.articles._city-index')
                            @endif
                            @if(str_starts_with($article->slug, \App\Support\CityGuideIndex::NATIONAL_SLUG . '-'))
                                @include('public.content.articles._city-listings')
                            @endif
                            <hr class="dark mt-5">
                            <p class="h4 mb-4">About the Author</p>
                            @foreach($article->authors as $author)
                            <div class="article-author-box mb-4">
                                <a href="{{ $author->about_page_url }}">
                                    <div class="d-md-flex align-items-center">
                                        <div class="flex-shrink-0 text-center mb-4 mb-md-0">
                                            <img src="@if ($author->team_photo) {{ $author->team_photo }} @else {{Vite::asset('resources/images/team/missing.jpg')}} @endif" alt="{{ $author->name }}, {{ $author->title }}"
                                                 width="120" height="120">
                                        </div>
                                        <div class="bg-gray-light py-4 py-md-0 px-4 align-self-stretch d-flex flex-column justify-content-center">
                                            <h3 class="name-title text-dark">
                                                <span>{{ $author->full_name }} @if($author->title), <em>{{ $author->title }}</em>@endif</span>
                                            </h3>
                                            <p class="bio mb-0 text-dark">{{ $author->short_bio }}</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                            <p class="articles-bottom-nav text-center mt-4">
                                @if($prevArticle)
                                <a href="{{ route('public.content.articles.show', $prevArticle->slug) }}" class="btn text-nowrap"><i class="fa-regular fa-arrow-left me-1"></i> Previous article</a>
                                @endif
                                @if($nextArticle)
                                <a href="{{ route('public.content.articles.show', $nextArticle->slug) }}" class="btn text-nowrap">Next article <i class="fa-regular fa-arrow-right me-1"></i></a>
                                @endif
                            </p>
                        </article>
                    </div>
                  </div>
                </div>
                <div class="after-single-post"></div>
            </div>
        </div>
    </div>
    <div id="post-newsletter" class="outline-newsletter-optin horizontal mb-4">
        <x-optins.newsletter />
    </div>
@endsection
