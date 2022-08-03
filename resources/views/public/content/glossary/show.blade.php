@extends('layouts.public')

@push('seo')
    {!! $glossaryItem->seo !!}
@endpush

@section('content')
    <div class="single-post-wrapper">
        <div class="container">
            <div class="single-post-content-container">
                <div class="breadcrumbs">
                    <a href="{{ route('home') }}" class="home">Spannr</a>
                    <span class="connector">&raquo;</span>
                    <a href="{{ route('public.content.glossary.index') }}" class="parent">Glossary</a>
                    <span class="connector">&raquo;</span>
                    <span class="current">{{ $glossaryItem->name }}</span>
                </div>

                <article class="single-post">
                    <div class="post-title">
                        <h1>{{ $glossaryItem->name }}</h1>
                    </div>
                    <div class="single-post-content">
                        {!! $glossaryItem->content !!}
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
