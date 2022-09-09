@extends('layouts.public')

@push('seo')
    {!! $article->seo !!}
@endpush

@section('content')
    <div class="single-post-wrapper">
        <div class="container">
            <div class="single-post-content-container">
                <div class="breadcrumbs">
                    <a href="{{ route('home') }}" class="home">Spannr</a>
                    <span class="connector">&raquo;</span>
                    <a href="{{ route('public.content.articles.index') }}" class="parent">Articles</a>
                    <span class="connector">&raquo;</span>
                    <span class="current">{{ $article->name }}</span>
                </div>

                <article class="single-post">
                    <h1 class="post-title">{{ $article->name }}</h1>
                    <div class="single-post-share-container mb-3">
                        <small>Share</small>
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
                    @if($article->image)
                        <img src="{{ $article->image_full_url }}" alt="{{ $article->name }}" class="mb-3">
                    @endif
                    <div class="single-post-content">
                        {!! $article->content !!}
                    </div>
                </article>
            </div>
            <div id="sidebar" class="sidebar">
                <div class="gray-newsletter-optin">
                    <x-optins.newsletter showNameFields="true" />
                </div>
            </div>
        </div>
    </div>
    <div id="post-newsletter" class="gray-newsletter-optin mb-4">
        <x-optins.newsletter />
    </div>
@endsection
