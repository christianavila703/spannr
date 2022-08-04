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
        <div class="breadcrumbs mt-5 mb-0">
            <a href="{{ route('home') }}" class="home">Spannr</a>
            <span class="connector">&raquo;</span>
            <a href="{{ route('public.content.organizations.index') }}" class="parent">Companies</a>
            <span class="connector">&raquo;</span>
            <span class="current">{{ $organization->name }}</span>
        </div>

        <div class="organization-details-container mb-5">
            <div class="heading-group border-bottom mb-4">
                <div class="logo" style="background-image: url({{ $organization->image_url }});"></div>
                <div class="text">
                    <span class="d-block text-muted text-uppercase fw-bold">Organization</span>
                    <h1 class="h2">{{ $organization->name }}</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="mb-4">
                        <h2 class="title">About</h2>
                        <p>{!! $organization->description !!}</p>
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
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-5 offset-xl-1">
                    <h2 class="title">Stock</h2>
                    <div class="stock-details">
                        <span class="ticker">
                            {{ $organization->stock->ticker }}
                        </span>
                        <span class="current-price">
                            ${{ $organization->stock->latestSnapshot->current_price }}
                        </span>
                        <span class="change-percent {{ ($organization->stock->latestSnapshot->previous_day_change_percent > 0) ? 'text-success' : 'text-danger' }}">
                            @if ($organization->stock->latestSnapshot->previous_day_change_percent > 0)
                                <i class="fa-duotone fa-circle-up text-success"></i>
                            @else
                                <i class="fa-duotone fa-circle-down text-danger"></i>
                            @endif
                            {{ $organization->stock->latestSnapshot->previous_day_change_percent }}%
                        </span>
                        <span class="change-dollar">
                            @if ($organization->stock->latestSnapshot->previous_day_change_price < 0)
                                @php $amount = $organization->stock->latestSnapshot->previous_day_change_price * -1; @endphp
                                $({{ $amount }})
                            @else
                                ${{ $organization->stock->latestSnapshot->previous_day_change_price }}
                            @endif
                        </span>
                    </div>
                    <div>
                        @include('public.content.organizations.chart', ['slug' => $organization->slug])
                    </div>
                </div>
                <div class="col-12 col-md-6 mt-5">
                    @if ($organization->mediaItems->count() > 0)
                        <h2 class="title">Latest News</h2>
                        <livewire:public.organization-news organizationId="{{ $organization->id }}"/>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
