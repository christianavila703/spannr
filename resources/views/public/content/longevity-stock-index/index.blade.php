@extends('layouts.public')

@section('content')
    <div class="longevity-stock-index-container container-fluid pt-5">
        @include('admin.includes.status-messages')

        <div class="bg-quintary p-3 fs-5 fw-bold text-center text-white">The Spannr Longevity Stock Index has been retired as of February 29, 2024 and will no longer be updated.</div>

        <div class="d-lg-flex align-items-start">
            <div class="overview d-flex flex-column flex-shrink-0 justify-content-center mt-xl-5 mb-3 pe-xl-5">
                <h1 class="text-primary text-center">Longevity <span class="d-block">Stock Index</span></h1>
                <div class="d-flex align-items-center justify-content-center">
                    <div class="h3 pe-3 my-1">{{ $marketSnapshot->formatted_index_value }}</div>
                    <div class="my-1 {{ $marketSnapshot->index_change_color }}">
                       <span class="me-1 lead">{!! $marketSnapshot->index_change_icon !!}</span>
                       <span class="h4 fw-bold">{{ $marketSnapshot->formatted_index_change }}</span>
                    </div>
                </div>
                <p class="mb-1 text-center small text-muted px-2">
                    <span class="me-3 text-nowrap">{{ $marketSnapshot->formatted_eastern_timestamp }}</span>
                    <span class="text-nowrap">Market Cap: {{ $marketSnapshot->formatted_market_cap }}</span>
                </p>
                <div class="d-flex flex-wrap justify-content-center">
                    <table class="table table-sm small width-auto" style="min-width: 200px">
                        <tr>
                            <td class="text-nowrap">Last</td><td class="text-right">{{ $marketSnapshot->formatted_index_value }}</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap">$ Change</td><td class="text-right">{{ $marketSnapshot->previous_dollar_change }}</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap">Open</td><td class="text-right">{{ $marketSnapshot->today_open }}</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap">High</td><td class="text-right">{{ $marketSnapshot->today_high }}</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap">Year Low</td><td class="text-right">{{ $marketSnapshot->year_low }}</td>
                        </tr>
                    </table>
                    <div>&nbsp;&nbsp;&nbsp;</div>
                    <table class="table table-sm small width-auto" style="min-width: 200px">
                        <tr>
                            <td class="text-nowrap">Volume</td><td class="text-right">{{ $marketSnapshot->volume }}</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap">% Change</td><td class="text-right">{{ $marketSnapshot->formatted_index_change }}</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap">Prev. Close</td><td class="text-right">{{ $marketSnapshot->previous_close }}</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap">Low</td><td class="text-right">{{ $marketSnapshot->today_low }}</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap">Year High</td><td class="text-right">{{ $marketSnapshot->year_high }}</td>
                        </tr>
                    </table>
                </div>
                <div class="mt-4">
                    <p class="m-0 max-width-320 mx-auto small text-center fst-italic">
                        The Spannr Longevity Stock Index is designed to track the performance of public companies that are developing medicine or technology that target the aging process to help extend lifespan.
                    </p>
                </div>
            </div>

            <div class="flex-grow-1 mx-auto me-xl-0">
                <div class="chart-container py-3 border-white">
                    @include('public.content.longevity-stock-index.chart')
                </div>
                <div class="mt-3 mt-lg-2 mt-xl-3 index-four-buttons-container">
                    <a href="{{ route('public.content.organizations.index') }}" class="btn btn-dark py-4">
                        <span>View Company Profiles</span>
                    </a>
                    <a href="{{ route('public.content.longevity-index.about') }}" class="btn btn-dark py-4">
                        <span>Methodology &amp; FAQ</span>
                    </a>
                    <a href="{{ route('public.content.ventures') }}" class="btn btn-dark py-4">
                        <span>Become an Investor</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-5 pb-3">
            <div class="article-cards-container six-columns">
                <div class="swiper article-carousel article-container">
                    <div class="swiper-wrapper">
                        @foreach ($articles as $article)
                            <div class="swiper-slide">
                                <div class="carousel-inner">
                                    <x-content.article-card :article="$article" shortExcerpt="true" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>

        <table class="longevity-stock-index-table table small align-middle">
            <thead>
            <tr>
                <th class="name">Name</th>
                <th class="weight">Weight</th>
                <th class="ticker">Ticker</th>
                <th class="current_price">Current Price</th>
                <th class="yesterday_close">Yesterday Close</th>
                <th class="market_cap">Market Cap</th>
                <th class="volume">Volume</th>
                <th class="today_high">Today's High</th>
                <th class="today_low">Today's Low</th>
                <th class="previous_change_dollar text-nowrap">$ Change</th>
                <th class="previous_change_percent text-nowrap">% Change</th>
                <th class="outstanding_shares">Outstanding Shares</th>
            </tr>
            </thead>
            <tbody>
            @foreach($marketSnapshot->stockSnapshots as $stockSnapshot)
                <tr id="snapshot-{{ $stockSnapshot->id }}">
                    <td class="name d-flex justify-content-between">
                        <a href="{{ route('public.content.organizations.show', $stockSnapshot->stock->organization->slug) }}">{{ $stockSnapshot->stock->name }}</a>
                        <button class="btn snapshot-toggle" aria-label="Toggle details for {{ $stockSnapshot->stock->ticker }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.2.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M224 397.3l22.6-22.6 160-160L429.3 192 384 146.7l-22.6 22.6L224 306.7 86.6 169.4 64 146.7 18.7 192l22.6 22.6 160 160L224 397.3z"/></svg>
                        </button>
                    </td>
                    <td class="weight">
                        {{ $stockSnapshot->weight_percent }}%
                    </td>
                    <td class="ticker">{{ $stockSnapshot->stock->ticker }}</td>
                    <td class="show-mobile current_price">${{ number_format($stockSnapshot->current_price, 2) }}</td>
                    <td class="show-mobile yesterday_close">${{ number_format($stockSnapshot->yesterday_close, 2) }}</td>
                    <td class="show-mobile market_cap">${{ number_format($stockSnapshot->market_cap, 0) }}</td>
                    <td class="show-mobile volume">{{ number_format($stockSnapshot->volume, 0) }}</td>
                    <td class="show-mobile today_high">${{ number_format($stockSnapshot->today_high, 2) }}</td>
                    <td class="show-mobile today_low">${{ number_format($stockSnapshot->today_low, 2) }}</td>
                    <td class="show-mobile previous_change_dollar">
                        <span class="{{ $stockSnapshot->change_color }}">
                            ${{ number_format($stockSnapshot->previous_day_change_price, 2) }}
                        </span>
                    </td>
                    <td class="show-mobile previous_change_percent">
                        {{ $stockSnapshot->previous_day_change_percent }}%
                    </td>
                    <td class="show-mobile outstanding_shares">{{ number_format($stockSnapshot->outstanding_shares, 0) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="container mt-5">
        <div class="bg-quintary p-3 fs-5 fw-bold text-center text-white">The Spannr Longevity Stock Index has been retired as of February 29, 2024 and will no longer be updated.</div>
    </div>
    <div class="container my-5 small">
        @if($seo)
            <div class="longevity-index-disclosure-box mb-3">
                {!! $seo->content !!}
            </div>
        @endif
    </div>
@endsection

@push('end')
    @vite('resources/js/article-carousel.js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script>
        jQuery(document).ready(function($) {
             $('.snapshot-toggle').on('click', function() {
                 if (window.innerWidth < 1024) {
                     $(this).parent().siblings('.show-mobile').slideToggle();
                     $(this).toggleClass('flipped');
                 }
             });
        });
    </script>
@endpush
