@extends('layouts.public')

@push('seo')
    <title>{{ $organization->name }} - Spannr</title>
    <meta name="description" content="{{ $organization->seo_description }}" />
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
    <link rel="canonical" href="{{ route('public.content.organizations.show', $organization->slug) }}" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="{{ $organization->name }} - Spannr" />
    <meta property="og:description" content="{{ $organization->seo_description }}" />
    <meta property="og:url" content="{{ route('public.content.organizations.show', $organization->slug) }}" />
    <meta property="og:site_name" content="Spannr" />
    <meta property="article:modified_time" content="2022-08-03T15:21:28+00:00" />
    <meta property="og:image" content="https://wp.spannr.com/wp-content/uploads/2022/04/social-share-white.jpg" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="628" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="@spannr_" />
@endpush

@section('content')
    <div class="container">
        <div class="breadcrumbs mt-4">
            <x-nav.breadcrumbs parentUrl="{{ route('public.content.organizations.index') }}" parentLabel="Longevity Companies" currentLabel="{{ $organization->name }}"/>
        </div>

        <div class="organization-details-container mb-5">
            <div class="heading-group border-bottom mb-4">
                <div class="logo mb-4" style="background-image: url({{ $organization->image_url }});"></div>
                <div class="text">
                    <span class="d-block text-muted text-uppercase fw-bold mb-2">{{ $organization->type }} Company</span>
                    <h1 class="h2 mb-0">{{ $organization->name }}</h1>
                    @if($organization->website)
                        <div class="small fw-bold mb-3">
                            <a href="{{ $organization->website }}" target="_blank" rel="noopener noreferrer">{{ $organization->website }}</a>
                        </div>
                    @endif
                </div>
                <div class="social-icons">
                    @if($organization->facebookUrl)
                        <a href="{!! $organization->facebookUrl !!}" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-facebook"></i></a>
                    @endif
                    @if($organization->twitterUrl)
                        <a href="{!! $organization->twitterUrl !!}" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-twitter"></i></a>
                    @endif
                    @if($organization->linkedinUrl)
                        <a href="{!! $organization->linkedinUrl !!}" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-linkedin"></i></a>
                    @endif
                    @if($organization->instagramUrl)
                        <a href="{!! $organization->instagramUrl !!}" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-instagram"></i></a>
                    @endif
                </div>
            </div>
            @if($organization->summary)
                <div class="mb-4 lead text-secondary">
                    {{ $organization->summary }}
                </div>
            @endif
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="mb-4">
                        <h2 class="title">About</h2>
                        <div>
                            {!! $organization->description !!}
                        </div>

                        @if ($organization->extended_description)
                            <div class="pb-3">
                                <a class="btn btn-gray-medium text-dark" data-bs-toggle="collapse" href="#extended-description" role="button" aria-expanded="false" aria-controls="extended-description">
                                    <span class="text">Read</span>
                                </a>
                                <div class="collapse" id="extended-description">
                                    <article class="last-child-no-me pt-4">
                                        {!! $organization->extended_description !!}
                                    </article>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="mb-3 overview">
                        <h3 class="title">Overview</h3>
                        <div class="row">
                            <div class="col-12 col-md-6 col-xl-5">
                                <div class="mb-3">
                                    <span class="label">Founded</span>
                                    <span class="value">{{ $organization->founded_date }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="label">Type</span>
                                    <span class="value">{{ $organization->type }}</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <span class="label">Location</span>
                                    @foreach ($organization->locations as $location)
                                        <span class="value">{{ $location->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @if ($organization->jobs_count > 0)
                            <div class="mt-4 mb-5">
                                <h2 class="title">Recent Jobs at {{ $organization->name }}</h2>
                                <livewire:public.organizations.organization-jobs organizationId="{{ $organization->id }}" />
                                <div>
                                    <a href="{{ route('public.content.jobs.index', ['search' => $organization->name] ) }}" class="btn btn-outline-dark">View Jobs &rarr;</a>
                                </div>
                            </div>
                        @endif
                        @if ($organization->media_items_count > 0)
                            <div class="mt-4">
                                <h2 class="title">Latest News</h2>
                                <livewire:public.organizations.organization-news organizationId="{{ $organization->id }}"/>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-5 offset-xl-1">
                    @if($organization->stock)
                        <div class="mb-5">
                            <h2 class="title">Stock</h2>
                            @include('public.content.organizations.stock-data')
                            @if ($organization->stock->data)
                                <div>
                                    @include('public.content.organizations.chart', ['slug' => $organization->slug])
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="mx-auto" style="max-width: 350px">
                        <div class="outline-newsletter-optin">
                            <x-optins.newsletter showNameFields="true" />
                        </div>
                        <div class="mt-5">
                            <livewire:public.random-glossary-item-widget />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
