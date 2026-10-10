@extends('layouts.public')

@push('seo')
<meta name="description" content="{{ $glossaryItem->meta_description }}" />
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
<link rel="canonical" href="{{ $glossaryItem->url }}" />
<meta property="og:locale" content="en_US" />
<meta property="og:type" content="glossary" />
<meta property="og:title" content="{{ $glossaryItem->name }}" />
<meta property="og:description" content="{{ $glossaryItem->meta_description }}" />
<meta property="og:url" content="{{ $glossaryItem->url }}" />
<meta property="og:site_name" content="Spannr" />
<meta property="og:image" content="https://wp.spannr.com/wp-content/uploads/2022/04/social-share-white.jpg" />
<meta property="og:image:width" content="1616" />
<meta property="og:image:height" content="824" />
<meta property="og:image:type" content="image/jpeg" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:creator" content="@spannr_" />
<meta name="twitter:site" content="@spannr_" />
{!! \App\Support\StructuredData::forGlossary($glossaryItem) !!}
@endpush

@section('content')
    <div class="single-post-wrapper">
        <div class="container">
            <div class="single-post-content-container">
                <x-nav.breadcrumbs parentUrl="{{ route('public.content.glossary.index') }}" parentLabel="Glossary" currentLabel="{{ $glossaryItem->name }}"/>
                <hr class="dark mb-5">

                <article class="single-post pb-4">
                    <div class="single-post-content-sidebar-container">
                        <article class="single-post-content">
                            <div class="post-content">
                                <h1 class="post-title post-title-xl mb-4">{{ $glossaryItem->name }}</h1>
                                <div>{!! $glossaryItem->content !!}</div>
                                @can('edit records')
                                    <a href="{{ route('admin.glossary.edit', $glossaryItem->id) }}" class="h6 text-tertiary">Edit Glossary Item</a>
                                @endcan
                            </div>
                        </article>
                        <div class="sidebar-container">
                            <div id="sidebar" class="sidebar">
                                <div id="sticky-stabilizer" style="height: 20px;"></div>
                                <div id="sticky-sidebar-contents">
                                    <div>
                                        <livewire:public.random-glossary-item-widget :excludedIds="[$glossaryItem->id]"/>
                                    </div>
                                    <div class="outline-newsletter-optin my-4">
                                        <x-optins.newsletter showNameFields="true" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>
@endsection
