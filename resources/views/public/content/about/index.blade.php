@extends('layouts.public')

@section('content')
    <div class="page-about">
        <div id="hero" class="hero">
            <div class="container">
                <p class="h1 mb-4">Welcome to <span class="text-warning-neon">Spannr</span>.</p>
                <p class="h2 mb-0">We are dedicated to helping people understand the biggest business and health story of all time: the unprecedented transformation of society that will result from extending human healthspan and lifespan.</p>
            </div>
        </div>

        <div id="about" class="bg-white pt-5 pt-xl-6 pb-5 pb-xl-6">
            <div class="container">
                <h1>About Us</h1>
                <hr class="dark mb-4">
                <div class="row">
                    <div class="'col-12 col-lg-6 about-text-content">
                        <p>From a broad perspective, it's easy to see the countless opportunities and challenges that come with living longer. Global healthcare systems will have to adapt, economies will have to shift, and individuals will have access to an array of new products and services that can greatly enhance their lives.</p>

                        <p>But the path to understanding and actively participating in this longevity trend can be complicated. Technical science, conflicting information, and complex data can all be barriers to entry.</p>

                        <p>That's where Spannr comes in.</p>

                        <p>We are here to make sense of the complex world of longevity and provide actionable opportunities for everyone to get involved.</p>

                        <p>Join us in bringing longevity to the forefront of human and economic progress.</p>
                    </div>
                    <div class="mt-2 col-12 col-lg-6 ps-xl-5">
                        <blockquote class="first">
                            Spannr - [ <strong>span</strong>-er ] - <em>noun</em> : a person interested in extending their healthspan and lifespan.
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>

        <div id="fuel" class="bg-white pb-5 pb-xl-6">
            <div class="container">
                <h3 class="h1">Adding Fuel To The Fire</h3>
                <hr class="dark mb-4">
                <div class="row">
                    <div class="'col-12 col-lg-6 about-text-content">
                        <p>After years of neglect, researchers and scientists are finally turning back the clock and making strides to defy aging. Combine that with emerging technologies, a greater understanding of human biology, and an influx of private capital – the field of aging (arguably the largest Total Addressable Market to ever exist) is here to stay.</p>

                        <p>The time has come to acknowledge longevity as its own distinct industry, separate from the broader biotech and healthcare sectors.</p>

                        <p>To help fuel this conversation and to spur growth and awareness, Spannr is empowering our community with data, indices, and tools that stretch far beyond your typical blog or hype-machine.</p>

                        <p>To stay up to date on all the latest happenings in the industry and to witness, first-hand, the growth of today’s longevity revolution in real-time, we welcome you to subscribe to our newsletter.</p>
                    </div>
                    <div class="mt-2 col-12 col-lg-6 ps-xl-5">
                        <blockquote class="second">
                            We're empowering our community with data, indices, and tools that stretch far beyond your typical blog or hype-machine.
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
                <h3 class="h1">Why should you trust us?</h3>
                <hr class="dark mb-4">
                <div class="row">
                    <div class="'col-12 col-lg-6 about-text-content">
                        <p>If this is a question you are quick to ask yourself, then you are our kind of reader.</p>

                        <p>We aim to earn your trust very simply: Providing the information we would want to receive if our roles were reversed. We won't claim to have all the answers, but we aim to give you everything you need to know to make your own decision.</p>

                        <p>As an independent organization, Spannr is self funded and free of pressure from outside sources. We don’t have any hidden agenda. Our goal is to grow based on the quality and value of our content and proprietary resources.</p>

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
