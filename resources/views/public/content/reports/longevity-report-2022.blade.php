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
                            <div id="sidebar" class="sidebar report-sidebar">
                                <div id="sticky-stabilizer" style="height: 20px;"></div>
                                <div id="sticky-sidebar-contents">
                                    @if($content->sidebar)
                                        {!! $content->sidebar !!}
                                    @endif
                                    <div class="mt-4 glossary-widget-container">
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
    <div id="return-top">
        <button id="return-top-button" class="btn btn-quaternary btn-dark-magenta-hover" aria-label="Return to top">
            <i class="fa-sharp fa-solid fa-up"></i>
        </button>
    </div>
    <div id="toc-toggle" class="fixed-bottom d-lg-none" style="display: none">
        <button id="toc-toggle-button" class="btn btn-dark flex-grow-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Toggle Table of Contents">
            <i class="fa-sharp fa-solid fa-list-tree me-2"></i>Table of Contents
        </button>
        <button id="hide-toc" class="btn btn-link text-dark" aria-label="Hide Table of Contents button" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Hide ToC Menu">
            <i class="fa-sharp fa-solid fa-xmark"></i>
        </button>
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
        let topButton = document.getElementById('return-top-button');
        topButton.addEventListener('click', function() {
            window.scrollTo(0, 0);
        });

        let tocButton = document.getElementById('toc-toggle-button');
        let sidebar = document.getElementById('sticky-sidebar-contents');

        tocButton.addEventListener('click', function() {
            sidebar.classList.toggle('fixed-bottom');
            console.log("clicked");
        });

        $( document ).ready(function() {
            $("#optin-1").appendTo("#optin-report1");
            $("#optin-2").appendTo("#optin-report2");

            function showReturnTopButton() {
                let window_top = $(window).scrollTop();
                let postContentTop = $('.post-content').offset().top;

                if (window.innerWidth >= 1024 && window_top > postContentTop) {
                    $('#return-top').show();
                } else {
                    $('#return-top').hide();
                }
            }

            function showTableOfContentsButton() {
                let window_top = $(window).scrollTop();
                let postContentTop = $('.post-content').offset().top;

                if (window.innerWidth < 1024) {
                    if (window_top > postContentTop) {
                        $('#toc-toggle').show();
                    } else {
                        $('#toc-toggle').hide();
                    }
                } else {
                    $('#toc-toggle').hide();
                }
            }

            $(window).on('scroll', function() {
                showReturnTopButton();
                showTableOfContentsButton();
            });

            $(window).on('load', function() {
                showReturnTopButton();
                showTableOfContentsButton();
            });

            $('.table-of-contents a').click(function() {
                $('#sticky-sidebar-contents').removeClass('fixed-bottom');
            });

            $('#hide-toc').on('click', function() {
                $('#toc-toggle').remove();
            });
        });
    </script>
@endpush
