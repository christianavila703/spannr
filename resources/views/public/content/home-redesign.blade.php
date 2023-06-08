@extends('layouts.public')

@section('content')
    <div class="page-redesign">
        <div id="home-hero" class="hero">
            <div class="content">
                <div class="inner">
                    <h1>Live healthier, <span>longer.</span></h1>
                    <div>
                        <p class="lead">Spannr is the easiest place to access the complex world of longevity. Discover how to increase your healthspan and extend your lifespan.</p>
                        <p class="lead">Join thousands of Spannr subscribers:</p>
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

        <div id="home-investors" class="fade-in-when-visible index-investors">
            <div class="container">
                <h2 class="h1 text-center">Invest in the future of humanity.</h2>
                <p class="text-center max-width-1100 mx-auto mb-2 lead">Across the globe, scientists are working on ways to extend healthy human lifespans beyond what was historically possible. Significant advances have attracted serious investment, and the force of this movement is changing the way we look at life.</p>
                <p class="text-center max-width-1100 mx-auto lead">Interested in investing in solutions to some of the world’s most pressing problems?</p>

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
                                    <img src="{{ Vite::asset('resources/images/ventures/network-logos.webp') }}" alt="The Spannr Syndicate" class="d-md-none" width="500" height="550">
                                    <iframe id="kittens" src="{{ route('public.content.ventures.logos') }}?embed=true" loading="lazy" style="width: 100%;height: 480px" class="d-none d-md-block"></iframe>
                                </div>
                            </div>
                            <div class="mt-3 mx-auto">
                                <a href="{{ route('public.content.ventures') }}" class="btn btn-lg btn-outline-dark">
                                    Visit Spannr <span>Ventures<i class="fa-sharp fa-solid fa-arrow-right ms-2"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-6">
            <div id="post-newsletter" class="outline-newsletter-optin horizontal homepage max-width-1100 mx-auto">
                <x-optins.newsletter />
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
