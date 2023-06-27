@extends('layouts.public')

@push('seo')
<x-content.content-meta :content="$content" />
@endpush

@section('content')
    <main class="page-organizations-list py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 col-xl-6 mb-5 mb-xl-0">
                    <div class="max-width-800 mx-auto">
                        <h1>Longevity Investors</h1>
                        <p>Longevity investors from around the world are now funneling in billions of dollars each year in pursuit of extended human healthspan and lifespan. With a global aging population fueling this growing trend, interest in human health combined with advances in biotech are only positioned to accelerate further.</p>
                        <p>Spannr has aggregated extensive financial data across public, private, non-profit, and crypto markets to bring context to investing trends within longevity. More data about specific investors and companies is available upon request.</p>
                        <div class="mt-4">
                            <a class="btn btn-outline-dark" href="#longevity-investors">Jump to Investors &rarr;</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6 mt-xl-3">
                    <h3 class="text-center h5 mb-0">Longevity Investors by Type</h3>
                    <div id="entities-by-type-pie-chart" data-chart="{{ route('public.content.investors.charts.investors-by-type') }}"></div>
                </div>
            </div>

            <div class="row my-5">
                <div class="col-12 col-xl-6">
                    <h3 class="text-center mb-0">
                        <span class="h4 d-block">Total Amount Raised</span>
                        <span class="h5 d-block text-secondary">Past 12 Months</span>
                    </h3>
                    <div id="investors-total-amount-raised-last-12-months" data-chart="{{ route('public.content.investors.charts.total-amount-raised-last-12-months') }}"></div>
                </div>
                <div class="col-12 col-xl-6">
                    <h3 class="text-center mb-0">
                        <span class="h4 d-block">Amount Raised in Top 10 Categories</span>
                        <span class="h5 d-block text-secondary">Year to Date</span>
                    </h3>
                    <div id="investors-ytd-amount-raised-per-category" data-chart="{{ route('public.content.investors.charts.ytd-amount-raised-per-category') }}"></div>
                </div>
            </div>

            <div class="row my-5">
                <div class="col-12 col-xl-6">
                    <h3 class="text-center mb-0">
                        <span class="h4 d-block">Fundraising Totals By Round Type</span>
                        <span class="h5 d-block text-secondary">All Spannr-Tracked Data</span>
                    </h3>
                    <div id="investors-amount-raised-per-round" data-chart="{{ route('public.content.investors.charts.amount-raised-per-round') }}"></div>
                </div>

                <div class="col-12 col-xl-6">
                    <h3 class="text-center mb-0">
                        <span class="h4 d-block">Average Funding Size By Round Type</span>
                        <span class="h5 d-block text-secondary">All Spannr-Tracked Data</span>
                        <span class="h5 d-block text-secondary"></span>
                    </h3>
                    <div id="investors-average-raised-per-round" data-chart="{{ route('public.content.investors.charts.average-raised-per-round') }}"></div>
                </div>
            </div>

            <div class="row my-5 my-xl-6">
                <div class="col-12 col-md-6 col-xl-4 order-1">
                    <h3 class="text-center mb-0">
                        <span class="h4 d-block">Top 10 Investors</span>
                        <span class="h5 d-block text-secondary"># of Fundraising Deals</span>
                    </h3>
                    <table class="table width-auto mx-auto">
                        @foreach ($topInvestorsByDealNumber as $topInvestor)
                            <tr>
                                <th>{{ $topInvestor->name }}</th>
                                <td>{{ $topInvestor->fundraising_rounds_count }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <div class="col-12 order-md-3 col-xl-4 align-items-center mt-4 order-xl-2 mt-xl-0">
                    <div class="h-100 d-flex align-items-center justify-content-center">
                        <div class="outline-newsletter-optin max-width-300">
                            <x-optins.newsletter showNameFields="true" />
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-4 my-5 my-md-0 order-md-2 order-xl-3">
                    <h3 class="text-center mb-0">
                        <span class="h4 d-block">Top 10 Companies</span>
                        <span class="h5 d-block text-secondary">by Funds Raised</span>
                    </h3>
                    <table class="table width-auto mx-auto">
                        @foreach ($topCompaniesByTotalRaised as $topCompany)
                            <tr>
                                <th>{{ $topCompany['name'] }}</th>
                                <td>${{ number_format($topCompany['total_raised'], 0) }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            <div class="my-5">
                <h3 class="h4 text-center">Cumulative Fundraising Timeline</h3>
                <iframe id="cumulativeTimelineIframe" style="width:100%;height: 600px" data-url="{{ route('public.content.investors.charts.year-over-year-bar-chart-race') }}"></iframe>
                <div class="text-end">
                    <button id="resetCumulativeTimeline" class="btn btn-dark btn-sm">Restart Animated Timeline</button>
                </div>
            </div>

            <div id="longevity-investors" class="pt-4">
                <h2 class="h1 text-center mb-3">Longevity Investors Directory</h2>
                <livewire:public.investors.investor-index />
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
    <script src="{{ Vite::asset('resources/js/entities-by-type-pie-chart.js') }}"></script>
    <script src="{{ Vite::asset('resources/js/investors-total-amount-raised-last-12-months.js') }}"></script>
    <script src="{{ Vite::asset('resources/js/investors-total-amount-raised-per-round.js') }}"></script>
    <script src="{{ Vite::asset('resources/js/investors-average-amount-raised-per-round.js') }}"></script>
    <script src="{{ Vite::asset('resources/js/investors-ytd-amount-raised-per-category.js') }}"></script>
    <script>
        let scrollTop = document.getElementById('longevity-investors').offsetTop;

        Livewire.on('gotoTop', () => {
            window.scrollTo({
                top: scrollTop,
                left: 15,
                behaviour: 'smooth'
            });
        });

        let cumulativeTimelineIframe = document.querySelector('#cumulativeTimelineIframe');
        let cumulativeTimelineUrl = cumulativeTimelineIframe.dataset.url;

        function startCumulativeTimeline() {
            cumulativeTimelineIframe.src = cumulativeTimelineUrl;
        }

        document.getElementById('resetCumulativeTimeline').addEventListener('click', function() {
           startCumulativeTimeline();
        });

        let cumulativeTimelineObserver = new IntersectionObserver(function(entries) {
            if(entries[0].isIntersecting === true)
                startCumulativeTimeline();
        }, { threshold: [0.10] });

        cumulativeTimelineObserver.observe(cumulativeTimelineIframe);
    </script>
@endpush
