@extends('layouts.public')

@section('content')
    <main class="page-organizations-list py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 col-xl-6 mb-5 mb-xl-0">
                    <div class="max-width-800 mx-auto">
                        <h1>Longevity Companies</h1>
                        <p>The longevity industry is growing rapidly with new companies emerging almost daily. That's why Spannr has created this companies directory so that readers can keep up with the space and learn more about the entities involved. The goal is to ensure that those interested in longevity have the most up to date and factual information that is publicly available.</p>
                        <p>Right now, these are the top 10 categories that longevity companies are involved with:</p>

                        <div class="row">
                            @foreach ($topTags as $tag => $count)
                                <div class="col-12 col-md-6 h6">
                                    <span class="min-width-45 d-inline-block text-quaternary">{{ $count }}</span><strong>{{ $tag }}</strong>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <a class="btn btn-outline-dark" href="#longevity-companies">Jump to Companies &rarr;</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6 mt-xl-3">
                    <h3 class="text-center h5 mb-0">Longevity Organizations by Type</h3>
                    <div id="organizations-by-type" data-chart="{{ route('public.content.organizations.charts.organizations-by-type') }}"></div>
                </div>
            </div>
            <div class="longevity-companies-map-container mt-6 mb-5 text-center">
                <h2 class="h4">Where are longevity companies located?</h2>
                <p>We've been mapping it. As of right now, we're watching <strong class="text-quaternary">{{ number_format($totalOrganizations, 0) }}</strong> organizations working in longevity.</p>
                <div id="organizations-locations-map" data-chart="{{ route('public.content.organizations.charts.organizations-locations-map') }}"></div>
                <div class="d-flex flex-wrap align-items-center justify-content-center mt-4">
                    <div class="map-legend-box organization-public me-4">
                        <span class="background"></span>
                        <span class="label">Public</span>
                    </div>
                    <div class="map-legend-box organization-private me-4">
                        <span class="background"></span>
                        <span class="label">Private</span>
                    </div>
                    <div class="map-legend-box organization-non-profit me-4">
                        <span class="background"></span>
                        <span class="label">Non-Profit</span>
                    </div>
                    <div class="map-legend-box organization-educational me-4">
                        <span class="background"></span>
                        <span class="label">Educational</span>
                    </div>
                    <div class="map-legend-box organization-other me-4">
                        <span class="background"></span>
                        <span class="label">Other</span>
                    </div>
                </div>
            </div>
            <div class="my-5">
                <div class="row">
                    <div class="col-12 col-lg-5">
                        <h2 class="h4 mb-3">Quick Facts about the Longevity Industry</h2>
                        <ul class="pe-xl-5 plain-bullet-list">
                            <li>
                                <div>
                                    <span class="d-block lead">
                                        Out of <strong class="text-quaternary">{{ number_format($totalOrganizations, 0) }}</strong> companies, <strong class="text-quaternary">{{ number_format($foundedAfterValue, 0) }}</strong> were started in 2010 or later
                                    </span>
                                    <span class="d-block text-secondary fw-bold">That's {{ number_format($foundAfterPercent, 0) }}% of longevity companies founded in the last {{ $foundedDifference }} years</span>
                                </div>

                            </li>
                            <li>
                                <div>
                                    <span class="d-block lead">
                                        We've catalogued <strong class="text-quaternary">{{ number_format($jobsCount, 0) }} jobs</strong> across {{ number_format($jobCategoriesCount, 0) }} categories
                                    </span>
                                    <span class="d-block text-secondary fw-bold">
                                        Top 3 companies with most jobs posted are:
                                    </span>
                                    <span class="d-block">
                                        @foreach ($topHiringCompanies as $hiringCompany)
                                            <a href="{{ route('public.content.organizations.show', $hiringCompany->slug) }}">{{ $hiringCompany->name }}</a>
                                            @if(!$loop->last) / @endif
                                        @endforeach
                                    </span>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <span class="d-block lead">
                                        Top locations for longevity companies are:
                                    </span>
                                    @foreach ($topLocations as $topLocation)
                                        <div class="d-flex align-items-center mt-1">
                                            <strong class="text-quaternary h6 mb-0 me-2 d-block">{{ $topLocation->organizations_count }}</strong>
                                            <span>{{ $topLocation->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-12 col-lg-7">
                        <p class="h5 text-center">Longevity Companies by Founded Date</p>
                        <div id="organizations-by-founded-date" data-chart="{{ route('public.content.organizations.charts.organizations-by-founded-date') }}"></div>
                    </div>
                </div>
            </div>
            <div id="longevity-companies" class="pt-5">
                <h2 class="h1 text-center mb-3">Longevity Companies Directory</h2>
                <livewire:public.organizations.organization-index />
            </div>
        </div>
    </main>
@endsection

@push('end')
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/map.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/usaLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="{{ Vite::asset('resources/js/organizations-by-type-pie-chart.js') }}"></script>
    <script src="{{ Vite::asset('resources/js/organizations-locations-map.js') }}"></script>
    <script src="{{ Vite::asset('resources/js/organizations-by-founded-date-chart.js') }}"></script>

    <script>
        let scrollTop = document.getElementById('longevity-companies').offsetTop;

        Livewire.on('gotoTop', () => {
            window.scrollTo({
                top: scrollTop,
                left: 15,
                behaviour: 'smooth'
            })
        })
    </script>
@endpush
