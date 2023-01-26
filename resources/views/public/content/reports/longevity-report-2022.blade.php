@extends('layouts.public')

@push('seo')
<title>{{ $content->name }} - Spannr</title>
<meta name="description" content="{{ $content->meta_description }}" />
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
<link rel="canonical" href="{{ $content->url }}" />
<meta property="og:locale" content="en_US" />
<meta property="og:type" content="article" />
<meta property="og:title" content="{{ $content->name }}" />
<meta property="og:description" content="{{ $content->meta_description }}" />
<meta property="og:url" content="{{ $content->url }}" />
<meta property="og:site_name" content="Spannr" />
<meta property="article:published_time" content="{{ $content->meta_published_time }}" />
<meta property="og:image" content="{{ $content->shareable_image_url }}" />
<meta property="og:image:width" content="1616" />
<meta property="og:image:height" content="824" />
<meta property="og:image:type" content="image/jpeg" />
<meta name="author" content="Spannr" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:creator" content="@spannr_" />
<meta name="twitter:site" content="@spannr_" />
<meta name="twitter:label1" content="Written by" />
<meta name="twitter:data1" content="Spannr" />
<meta name="twitter:label2" content="Est. reading time" />
<meta name="twitter:data2" content="{{ $content->reading_time_minutes }} minutes" />
{!! $graph !!}
@endpush

@section('content')
    <div class="single-post-wrapper">
        <div class="container">
            <div class="single-post-content-container pt-4">

                <div class="single-post pb-4">
                    <h1 class="post-title post-title-xl mb-4 text-center">{{ $content->name }}</h1>
                    @if($featuredImage)
                        <img src="{{ $featuredImage }}" alt="{{ $content->name }}" class="mb-3" width="1616" height="824">
                    @endif
                    <div class="single-post-content-sidebar-container">
                        <article class="single-post-content pb-6">
                            <div class="post-meta-container justify-content-center">
                                @can('edit articles')
                                    <div class="author">
                                        <span class="ms-3">
                                            <a href="{{ route('admin.content.edit', $content->id) }}" class="text-tertiary">Edit</a>
                                        </span>
                                    </div>
                                @endcan
                                <div class="single-post-share-container">
                                    <a href="{{ $content->shareable_twitter_url }}" class="social-icon" target="_blank" rel="noopener noreferrer"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Share on Twitter">
                                        <i class="fa-brands fa-twitter"></i>
                                    </a>
                                    <a href="{{ $content->shareable_facebook_url }}" class="social-icon" target="_blank" rel="noopener noreferrer"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Share on Facebook">
                                        <i class="fa-brands fa-facebook"></i>
                                    </a>
                                    <a href="{{ $content->shareable_linkedin_url }}" class="social-icon" target="_blank" rel="noopener noreferrer"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Share on LinkedIn">
                                        <i class="fa-brands fa-linkedin-in"></i>
                                    </a>
                                    <a href="{{ $content->shareable_reddit_url }}" class="social-icon" target="_blank" rel="noopener noreferrer"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Share on Reddit">
                                        <i class="fa-brands fa-reddit"></i>
                                    </a>
                                    <a href="{{ $content->shareable_hacker_news_url }}" class="social-icon" target="_blank" rel="noopener noreferrer"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Share on Hacker News">
                                        <i class="fa-brands fa-hacker-news"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="post-content">
                                {!! $content->content !!}
                                <div id="optin-1">
                                    <x-optins.report />
                                </div>
                                <div id="optin-2">
                                    <x-optins.report />
                                </div>
                            </div>
                        </article>
                        <div class="sidebar-container">
                            <div id="sidebar" class="sidebar">
                                <div id="sticky-stabilizer" style="height: 20px;"></div>
                                <div id="sticky-sidebar-contents">
                                    <h3 class="h5">Table of Contents</h3>
                                    <ol class="table-of-contents mb-5">
                                        <li>
                                            <a href="#intro">Introduction</a>
                                        </li>
                                        <li>
                                            <a href="#about">About the Author</a>
                                        </li>
                                        <li>
                                            <a href="#summary">Executive Summary</a>
                                        </li>
                                        <li>
                                            <a href="#overview">2022 Fundraising Overview</a>
                                            <ul>
                                                <li>
                                                    <a href="#deals-by-location">Funding Deals by Location</a>
                                                </li>
                                                <li>
                                                    <a href="#deals-by-type">Funding Deals by Type</a>
                                                </li>
                                                <li>
                                                    <a href="#deals-by-size">Funding Deals by Size</a>
                                                </li>
                                                <li>
                                                    <a href="#average-deals">Average Deal Size</a>
                                                </li>
                                                <li>
                                                    <a href="#monthly">Month-over-Month</a>
                                                </li>
                                                <li>
                                                    <a href="#quarterly">Quarterly</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <a href="deep-dive">The Deep Dive</a>
                                        </li>
                                    </ol>
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
@endsection

@push('end')
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    @vite('resources/js/sticky-sidebar.js')
    <style>
        .sticky-sidebar {
            top: 20px;
        }
        #optin-1 {
            margin-bottom: 3rem;
        }
    </style>
    <script>
        $( document ).ready(function() {
            $("#optin-1").appendTo("#optin-report1");
        });
        $( document ).ready(function() {
            $("#optin-2").appendTo("#optin-report2");
        });
    </script>
@endpush
