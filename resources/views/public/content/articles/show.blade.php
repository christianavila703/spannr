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
                    <div class="post-title {{ ($article->image) ? 'has-image' : '' }}">
                        <div class="overlay">
                            <h1>{{ $article->name }}</h1>
                        </div>
                        @if($article->image)
                            <img src="{{ $article->image_full_url }}" alt="{{ $article->name }}">
                        @endif
                    </div>
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
