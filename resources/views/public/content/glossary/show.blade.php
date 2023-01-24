@extends('layouts.public')

@push('seo')
    {!! $glossaryItem->seo !!}
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
