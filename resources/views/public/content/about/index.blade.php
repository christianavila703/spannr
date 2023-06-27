@extends('layouts.public')

@section('content')
    <div class="page-about">
        <div id="hero" class="hero">
            <div class="container">
                <p class="h1 mb-4">Welcome to <span class="text-warning-neon">Spannr</span>.</p>
                <p class="h2 mb-0">We are dedicated to those searching to increase their healthspan and extend their lifespan. From education, to industry data, to product and service analysis, Spannr is here to demystify your health journey and increase the span of your life.</p>
            </div>
        </div>

        <div id="about" class="bg-white pt-5 pt-xl-6">
            <div class="container">
                <h1>About Us</h1>
                <hr class="dark mb-4">
                <div class="row">
                    <div class="'col-12 col-lg-6 about-text-content">
                        <p>Disruption is coming to healthcare, with emphasis on healthspan and lifespan leading the change. Countless opportunities and challenges will arise in the coming years as economies shift to accommodate changing demographics. Individuals will soon have access to an array of new products and services that can greatly enhance their lives.</p>

                        <p>But the path to understanding and actively participating in this longevity trend can be complicated. Technical science, conflicting information, and complex data can all be barriers to entry.</p>

                        <p>That's where Spannr comes in.</p>

                        <p>We are here to simplify access to the complex world of longevity and provide actionable opportunities for everyone to get involved.</p>

                        <p>Join us in bringing longevity to the forefront of human progress.</p>
                    </div>
                    <div class="mt-2 col-12 col-lg-6 ps-xl-5">
                        <blockquote class="first">
                            Spannr - [ <strong>span</strong>-er ] - <em>noun</em> : a person interested in extending their healthspan and lifespan.
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>

        <div id="fuel" class="bg-white pt-5 pt-xl-6 pb-5 pb-xl-6">
            <div class="container">
                <h3 class="h1">Adding Fuel To The Fire</h3>
                <hr class="dark mb-4">
                <div class="row">
                    <div class="'col-12 col-lg-6 about-text-content">
                        <p>Emerging technologies, a greater understanding of human biology, and an influx of private capital has finally given fuel that researchers and scientists need to make strides in defying aging. Extending human healthspan and lifespan (arguably the largest Total Addressable Market to ever exist) is here to stay.</p>

                        <p>The time has come to recognize longevity, with aims of extending healthspan and lifespan, as a legitimate industry that will integrate with the broader biotech and healthcare sectors.</p>

                        <p>To help fuel this conversation and to spur growth and awareness, Spannr is empowering our community with unique content, proprietary data, and custom tools that stretch far beyond a simple blog, news site, or marketplace.</p>

                        <p>Stay up to date on all the latest happenings in the industry and learn how to get involved personally by subscribing to our newsletter.</p>
                    </div>
                    <div class="mt-2 col-12 col-lg-6 ps-xl-5">
                        <blockquote class="second">
                            We're empowering our community with unique content, proprietary data, and custom tools that stretch far beyond a simple blog, news site, or marketplace.
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>

        <div id="newsletter" class="py-5 text-white">
            <div class="container">
                <div class="d-flex flex-wrap align-items-center justify-content-center">
                    <p class="lead m-4">
                        Join us for everything you need to know about the quest to extend healthy human lifespans.
                    </p>
                    <div class="form-container pt-3 px-4">
                        <livewire:forms.newsletter-opt-in
                            form="{{ \App\Models\FormEntry::FORM_NEWSLETTER_ABOUT }}"
                            formClass="email-signup-horizontal-purple-hover"
                            successClass="lead text-white fw-bold"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div id="trust" class="bg-white pt-5 pt-xl-6 pb-5 pb-xl-6">
            <div class="container">
                <h3 class="h1">Why Should You Trust Us?</h3>
                <hr class="dark mb-4">
                <div class="row">
                    <div class="'col-12 col-lg-6 about-text-content">
                        <p>We aim to earn your trust very simply: Providing the information we would want to receive if our roles were reversed. We won't claim to have all the answers, but we aim to give you everything you need to know to make your own decisions.</p>

                        <p>As an independent organization, Spannr is self funded and free of pressure from outside sources. We don’t have any hidden agendas. Our goal is to grow based on the quality and value of our content and proprietary resources.</p>

                        <p>If you have any questions about Spannr or want to get to know us better, <a href="{{ route('public.content.contact') }}">contact us</a> – we'd love to hear from you. We appreciate your interest in what we are creating.</p>
                    </div>
                    <div class="mt-2 col-12 col-lg-6 ps-xl-5">
                        <blockquote class="third">
                            Spannr is self funded and free of pressure from outside sources. <strong>No hidden agendas.</strong>
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>

        <div id="team" class="bg-white pb-5">
            <div class="container">
                <h2 class="h1">Our Team</h2>
                <hr class="dark mb-5">
                <div class="row">
                    @foreach ($team as $person)
                        <x-content.about-team-card :person="$person" />
                    @endforeach
                </div>
            </div>
        </div>

        <div id="contributors" class="container pb-5">
            <h2 class="h1">Contributors</h2>
            <hr class="dark mb-5">
            <div class="row">
                @foreach ($contributors as $person)
                    <x-content.about-team-card :person="$person" />
                @endforeach
            </div>
        </div>

    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/ScrollTrigger.min.js"></script>
    @vite('resources/js/about.js')
@endsection
