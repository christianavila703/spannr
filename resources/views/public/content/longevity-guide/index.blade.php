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
<meta property="og:image:width" content="1600" />
<meta property="og:image:height" content="900" />
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
    <div class="longevity-guide-hero">
        <div class="content">
            <img src="{{ Vite::asset('resources/images/dna-hills.svg') }}" alt="DNA-shaped hills">
            <object type="image/svg+xml" data="{{ Vite::asset('resources/images/longevity-guy.svg') }}" id="longevity-guy"></object>
            <object type="image/svg+xml" data="{{ Vite::asset('resources/images/animated-clouds.svg') }}" id="animated-clouds"></object>
        </div>
    </div>
    <div class="single-post-wrapper">
        <div class="container">
            <div class="single-post-content-container pt-4">

                <div class="single-post pb-4">
                    <div class="single-post-content-sidebar-container">
                        <article class="single-post-content full-width pb-6">
                            <h1 class="post-title post-title-xl mb-3 text-center">{{ $content->name }}</h1>
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
                                <div class="main-content">
                                    {!! $content->content !!}
                                </div>
                                <div id="table-of-contents" class="table-of-contents-box">
                                    {!! $content->sidebar !!}
                                </div>
                                @foreach($content->children as $childContent)
                                    <div class="mt-5" id="{{ $childContent->slug }}">
                                        {!! $childContent->content !!}
                                    </div>
                                    @if($childContent->sources)
                                        <div id="article-citations">
                                            <div>
                                                <a class="h4 citation-title" data-bs-toggle="collapse" data-bs-target="#article-sources-{{ $childContent->slug }}"
                                                   aria-expanded="false" aria-controls="article-sources-{{ $childContent->slug }}" aria-label="Expand sources">
                                                    <span class="icon"></span><span>{{ $childContent->sources_label ?? 'Sources' }}</span>
                                                </a>
                                            </div>
                                            <div class="collapse" id="article-sources-{{ $childContent->slug }}">
                                                <div class="fs-6">
                                                    {!! $childContent->sources !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="return-top" class="always">
        <button id="return-top-button" class="btn btn-quaternary btn-dark-magenta-hover" aria-label="Return to top">
            <i class="fa-sharp fa-solid fa-up"></i>
        </button>
    </div>
@endsection

@push('end')
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script>
        let topButton = document.getElementById('return-top-button');
        topButton.addEventListener('click', function() {
            $([document.documentElement, document.body]).animate({
                scrollTop: $("#table-of-contents").offset().top
            });
        });

        $( document ).ready(function() {
            function showReturnTopButton() {
                let window_top = $(window).scrollTop();
                let postContentTop = $('.post-content').offset().top;

                if (window_top > postContentTop) {
                    $('#return-top').show();
                } else {
                    $('#return-top').hide();
                }
            }

            $(window).on('scroll', function() {
                showReturnTopButton();
            });

            $(window).on('load', function() {
                showReturnTopButton();
            });
        });

    </script>
@endpush
