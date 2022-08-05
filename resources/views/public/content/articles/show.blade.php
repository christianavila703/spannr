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
                    @if($article->image)
                        <img src="{{ $article->image_full_url }}" alt="{{ $article->name }}" class="mb-3">
                    @endif
                    <div class="single-post-content">
                        {!! $article->content !!}
                    </div>
                </article>
            </div>
            <div id="sidebar" class="sidebar">
                <p>Join us for everything you need to know about the quest to extend healthy human lifespans.</p>
                @include('public.includes.mailchimp-sidebar')
            </div>
        </div>
    </div>
@endsection
