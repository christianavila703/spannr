@extends('layouts.public')

@section('content')
    <div class="page-redesign">
        <div id="home-hero" class="hero">
            <div class="content">
                <div class="inner">
                    <h1>We cover the industry of <span>life extension</span></h1>
                    <div>
                        <p>Spannr is the <strong>easiest</strong> place to stay up to date on the complex world of longevity, age-defying research, and the businesses shaping the future of healthcare.</p>
                        <p>Join thousands of longevity leaders today.</p>
                    </div>
                    <div class="mailchimp-form">
                        <livewire:forms.newsletter-opt-in
                            form="{{ \App\Models\FormEntry::FORM_NEWSLETTER_HOMEPAGE_HERO }}"
                            formClass="email-signup-horizontal-white"
                            successClass="lead text-warning fw-bold"
                        />
                    </div>
                </div>
            </div>
            <div class="image">
                <div class="inner">
                    <div class="mobile-phone parallax" data-depth="0.10"></div>
                </div>
            </div>
        </div>

        <div id="home-insights" class="article-cards-container container py-6">
            <p class="mb-3 h5 text-quaternary">Most Popular</p>
            <h2 class="mb-0">Our Top Insights:</h2>
            <hr class="dark mb-5">
            <div class="article-container">
                @foreach($articles as $article)
                    <x-content.article-card :article="$article" />
                @endforeach
            </div>
            <div class="mt-5 text-center">
                <a href="{{ route('public.content.articles.index') }}" class="btn btn-lg btn-outline-dark">Explore More Insights</a>
            </div>
        </div>

        <div id="home-investors" class="fade-in-when-visible index-investors bg-light-blue py-6">
            <div class="container">
                <h2 class="h1 text-center">Invest in the future <span class="d-block">of humanity.</span></h2>
                <p class="text-center max-width-1080 mx-auto mb-2">Across the globe, scientists are working on ways to extend healthy human lifespans beyond what was historically possible. Significant advances have attracted serious investment, and the force of this movement is changing the way we look at life.</p>
                <p class="text-center max-width-1080 mx-auto">Interested in investing in solutions to some of the world’s most pressing problems?</p>

                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="d-flex flex-column justify-content-between align-items-stretch h-100">
                            <div class="w-100 text-center">
                                <h3 class="h4">Invest in the Public Markets</h3>
                                <div class="chart-container mt-3">
                                    @include('public.content.longevity-stock-index.chart-homepage')
                                </div>
                            </div>
                            <div class="mt-3 mx-auto">
                                <a href="{{ route('public.content.longevity-index') }}" class="btn btn-lg btn-outline-dark">
                                    Explore Index<i class="fa-sharp fa-solid fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mt-5 mt-lg-0">
                        <div class="d-flex flex-column justify-content-between align-items-stretch h-100">
                            <div class="w-100 text-center">
                                <h3 class="h4">Invest in Private Startups</h3>
                                <div class="image-container max-width-500 mx-auto">
                                    <img src="{{ Vite::asset('resources/images/ventures/network.webp') }}" alt="The Spannr Syndicate" class="d-md-none">
                                    <iframe id="kittens" src="{{ route('public.content.ventures.logos') }}?embed=true" loading="lazy" style="width: 100%;height: 480px" class="d-none d-md-block"></iframe>
                                </div>
                            </div>
                            <div class="mt-3 mx-auto">
                                <a href="{{ route('public.content.ventures') }}" class="btn btn-lg btn-outline-dark">
                                    Visit Spannr Ventures<i class="fa-sharp fa-solid fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container py-6">
            <h2>Explore the World of Longevity</h2>
            <p class="mb-3 h5 text-quaternary">Learn more about the longevity companies changing the industry.</p>
            <hr class="dark mb-5">
            <div class="fade-in-when-visible">
                <div class="explore-companies">
                    <div class="swiper company-carousel">
                        <div class="swiper-wrapper">
                            @foreach($organizations as $organization)
                                <div class="swiper-slide">
                                    <div class="carousel-inner">
                                        <a href="{{ route('public.content.organizations.show', $organization->slug) }}" class="company-carousel-item">
                                            <div class="logo" style="background-image: url('{{ $organization->image_full_url }}');">
                                                <span class="visually-hidden">{{ $organization->name }}</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
                <div class="mt-5 text-center">
                    <a href="{{ route('public.content.organizations.index') }}" class="btn btn-lg btn-outline-dark">Explore More Companies</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('end')
    @vite('resources/js/company-carousel.js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.3/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.3/ScrollTrigger.min.js"></script>
    @vite('resources/js/home.js')
@endpush
