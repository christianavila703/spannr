@extends('layouts.public')

@push('seo')
{{-- {!! $article->seo !!} --}}

@php
/*
 SPANNR GENERATED
// TODO: We may use the WP-generated SEO above for launch, depending
og:image:width
og:image:height
og:image:type
  */
@endphp
<title>{{ $article->name }} - Spannr</title>
<meta name="description" content="{{ $article->meta_description }}" />
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
<link rel="canonical" href="{{ $article->url }}" />
<meta property="og:locale" content="en_US" />
<meta property="og:type" content="article" />
<meta property="og:title" content="{{ $article->name }}" />
<meta property="og:description" content="{{ $article->meta_description }}" />
<meta property="og:url" content="{{ $article->url }}" />
<meta property="og:site_name" content="Spannr" />
<meta property="article:published_time" content="{{ $article->meta_published_time }}" />
<meta property="og:image" content="{{ $article->shareable_image_url }}" />
<meta property="og:image:width" content="1616" />
<meta property="og:image:height" content="824" />
<meta property="og:image:type" content="image/jpeg" />
<meta name="author" content="{{ $article->author_byline }}" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:creator" content="@spannr_" />
<meta name="twitter:site" content="@spannr_" />
<meta name="twitter:label1" content="Written by" />
<meta name="twitter:data1" content="{{ $article->author_byline }}" />
<meta name="twitter:label2" content="Est. reading time" />
<meta name="twitter:data2" content="{{ $article->reading_time_minutes }} minutes" />
{!! $graph !!}
@endpush

@section('content')
    <div class="single-post-wrapper">
        <div class="container">
            <div class="single-post-content-container">
                <x-nav.breadcrumbs parentUrl="{{ route('public.content.articles.index') }}" parentLabel="Insights" currentLabel="{{ $article->name }}"/>
                <hr class="dark mb-5">

                <div class="single-post pb-4">
                    <h1 class="post-title post-title-xl mb-4">{{ $article->name }}</h1>
                    @if($featuredImage)
                        <img src="{{ $featuredImage }}" alt="{{ $article->name }}" class="mb-3 w-100" width="1616" height="824">
                    @elseif($article->image)
                        <img src="{{ $article->image_full_url }}" alt="{{ $article->name }}" class="mb-3 w-100" width="1616" height="824">
                    @endif
                    <div class="single-post-content-sidebar-container">
                        <article class="single-post-content pb-6">
                            <div class="post-meta-container">
                                <div class="author">
                                    @if($article->author)
                                        By <a href="{{ $article->author->about_page_url }}">{{ $article->author->byline ?? $article->author->name }}</a>
                                    @else
                                        By Spannr
                                    @endif
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
                            <hr class="dark mt-5">
                            <div class="article-author-box">
                                <p class="h4 mb-4">About the Author</p>
                                <a href="{{ $article->author->about_page_url }}">
                                    <div class="d-md-flex align-items-center">
                                        <div class="flex-shrink-0 text-center mb-4 mb-md-0">
                                            <img src="@if ($article->author->team_photo) {{ $article->author->team_photo }} @else {{Vite::asset('resources/images/team/missing.jpg')}} @endif" alt="{{ $article->author->name }}, {{ $article->author->title }}"
                                                 width="120" height="120">
                                        </div>
                                        <div class="bg-gray-light py-4 py-md-0 px-4 align-self-stretch d-flex flex-column justify-content-center">
                                            <h3 class="name-title text-dark">
                                                <span>{{ $article->author->full_name }}, <em>{{ $article->author->title }}</em></span>
                                            </h3>
                                            <p class="bio mb-0 text-dark">{{ $article->author->short_bio }}</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </article>
                        <div class="sidebar-container">
                            <div id="sidebar" class="sidebar">
                                <div id="sticky-stabilizer" style="height: 20px;"></div>
                                <div id="sticky-sidebar-contents">
                                    @if($article->sidebar)
                                        <div>{!! $article->sidebar !!}</div>
                                    @endif
                                    <div class="outline-newsletter-optin">
                                        <x-optins.newsletter showNameFields="true" />
                                    </div>
                                    <div class="mt-4">
                                        <livewire:public.random-glossary-item-widget />
                                    </div>
                                </div>
                            </div>
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

@push('end')
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    @vite('resources/js/sticky-sidebar.js')
    <style>
        .sticky-sidebar {
            top: 20px;
        }
    </style>
@endpush
