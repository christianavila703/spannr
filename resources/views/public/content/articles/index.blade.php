@extends('layouts.public')

@section('content')
    <div class="single-post-wrapper">
        <div class="container pt-5 pt-lg-6 pb-5">
            <h1>Insights</h1>
            <p class="mb-3 h5 text-quaternary">Everything you need to know about all things longevity</p>
            <hr class="dark mb-4">
            <div class="single-post-content-container">
                <article class="single-post archive">
                    <div class="image-container">
                        <a href="{{ route('public.content.reports.longevity-report-2022') }}">
                            <img src="https://spannr.com/storage/media/11/reports--research--2022-longevity-funding.webp" alt="Spannr 2022 Longevity Funding Report"
                                 class="mb-3 w-100" width="1600" height="900">
                        </a>
                    </div>
                    <div class="text-container">
                        <div class="max-width-540 mx-auto">
                            <a href="{{ route('public.content.reports.longevity-report-2022') }}" class="post-title text-black">
                                <h2 class="h1">Spannr 2022 Longevity Funding Report</h2>
                            </a>
                            <div class="single-post-content archive-excerpt lead mb-4">
                                As a growing body of research shows that aging might not be as inevitable as we assumed, billions of dollars are beginning to pour into longevity startups. To help readers better understand today’s nascent longevity venture market, we’ve compiled all the fundraising data we independently collected for longevity-related companies in 2022.
                            </div>
                            <div class="link">
                                <a href="{{ route('public.content.reports.longevity-report-2022') }}" class="link-black-underline-gradient d-inline-block text-black"><span>Read More</span></a>
                            </div>
                        </div>
                    </div>
                </article>
                <hr class="dark" style="margin-bottom: 2rem">
                <livewire:articles.index />
            </div>
        </div>
    </div>
@endsection
