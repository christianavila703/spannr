@extends('layouts.public')

@section('content')
    <div class="single-post-wrapper">
        <div class="container pt-5 pt-lg-6 pb-5">
            <h1>Insights</h1>
            <p class="mb-3 h5 text-quaternary">Everything you need to know about all things longevity</p>
            <hr class="dark mb-4">
            <div class="single-post-content-container">
                @if ($report)
                    <article class="single-post archive">
                        <div class="image-container">
                            @if ($reportImage)
                                <a href="{{ $report->link }}">
                                    <img src="{{ $reportImage }}" alt="{{ $report->name }}"
                                         class="mb-3 w-100" width="1600" height="900">
                                </a>
                            @endif
                        </div>
                        <div class="text-container">
                            <div class="max-width-540 mx-auto">
                                <a href="{{ $report->link }}" class="post-title text-black">
                                    <h2 class="h1">{{ $report->name }}</h2>
                                </a>
                                <div class="single-post-content archive-excerpt lead mb-4">
                                    {{ $report->meta_description }}
                                </div>
                                <div class="link">
                                    <a href="{{ $report->link }}" class="link-black-underline-gradient d-inline-block text-black"><span>Read More</span></a>
                                </div>
                            </div>
                        </div>
                    </article>
                    <hr class="dark" style="margin-bottom: 2rem">
                @endif
                <livewire:articles.index />
            </div>
        </div>
    </div>
@endsection
