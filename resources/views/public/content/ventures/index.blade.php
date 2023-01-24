@extends('layouts.public')

@section('content')
<main class="page-ventures">
    <section class="ventures-hero-container">
        <div class="container">
            <div class="image-container">
                <div class="hero-image">
                    <img src="{{ Vite::asset('resources/images/ventures/ventures-hero.webp') }}" alt="Invest in the future of humanity">
                </div>
            </div>
            <div class="content">
                <div>
                    <p>Across the globe, scientists are working on ways to extend healthy human lifespans beyond what we know. Significant advances have attracted serious investment and the force of this movement is changing the way we look at life.</p>
                    <h1>Invest in the future of humanity.</h1>
                    <a href="#signup" class="btn btn-rounded btn-outline">Sign Up</a>
                </div>
            </div>
        </div>
    </section>

    <section class="ventures-about-container">
        <div class="container">
            <div class="content">
                <h2 class="h1">The Spannr <span>syndicate</span></h2>
                <p>The Spannr syndicate gives you direct, exclusive access to highly-curated longevity investments alongside
a number of like-minded investors. Through our experience-led, data-driven investment selection process,
you can be confident that the deals you see are some of the best opportunities in the industry.</p>
            </div>
            <div class="image-container">
                <img src="{{ Vite::asset('resources/images/ventures/network.webp') }}" alt="The Spannr Syndicate">
            </div>
        </div>
    </section>

    <section class="ventures-reasons-container py-6">
        <div class="container text-center">
            <h2 class="h1">Become a Spannr syndicate member in minutes and get exclusive access to:</h2>
            <div class="row">
                <div class="col-12 col-lg-4">
                    <h3 class="reason-title">Our Network</h3>
                    <div class="reason-stat">800+</div>
                    <div class="reason-subtitle">
                        <span>Companies</span><span>Tracked</span>
                    </div>
                </div>
                <div class="col-12 col-lg-4 mt-5 pt-3 pt-lg-0 mt-lg-0">
                    <h3 class="reason-title">Our Investment Expertise</h3>
                    <div class="reason-stat">60+</div>
                    <div class="reason-subtitle">
                        <span>Past</span><span>Investments</span>
                    </div>
                </div>
                <div class="col-12 col-lg-4 mt-5 pt-3 pt-lg-0 mt-lg-0">
                    <h3 class="reason-title">Our Platform</h3>
                    <div class="reason-stat">100+</div>
                    <div class="reason-subtitle">
                        <span>Syndicate</span><span>Members</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="ventures-investments-container">
        <div class="container text-center">
            <h2 class="h1 mx-auto">Some of the companies our team has invested in or advised</h2>
            <div class="investment-logos-container">
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/juvenescence.png') }}" alt="Juvenescence">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/rejuveron.png') }}" alt="Rejuveron">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/character.png') }}" alt="Character">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/rejuvenation-tech.png') }}" alt="Rejuvenation Technologies">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/kernalbio.png') }}" alt="kernalbio">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/blackrock-neurotech.png') }}" alt="Blackrock Neurotech">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/mind-portal.png') }}" alt="MindPortal">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/paradromics.png') }}" alt="Paradromics">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/colossal.png') }}" alt="Colossal">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/endiatx.png') }}" alt="Endiatx">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/aria.png') }}" alt="ARIA Pharmaceuticals">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/rain.png') }}" alt="RAIN Neuromorphics">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/gerostate-alpha.png') }}" alt="Gerostate Alpha">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/agerate.png') }}" alt="Agerate">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/vessel.png') }}" alt="Vessel">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/tripp.png') }}" alt="Tripp">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/sensorium.png') }}" alt="Sensorium">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/sana-health.png') }}" alt="Sana Health">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/psilera.png') }}" alt="Psilera">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/muse.png') }}" alt="Muse">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/maya-health.png') }}" alt="Maya Health">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/gilgamesh.png') }}" alt="Gilgamesh">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/bright-minds.png') }}" alt="Bright Minds">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/atai.png') }}" alt="atai Life Sciences">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/beckley-psytech.png') }}" alt="Beckley Psytech">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/apollo.png') }}" alt="Apollo Neuro">
                </div>
            </div>
        </div>
    </section>

    <section id="signup" class="ventures-form-container">
        <div class="container">
            <h3 class="h1 text-center">Join us to learn more</h3>
            <livewire:forms.investor-opt-in formType="ventures" formClass="ventures-optin-form" successClass="lead fw-bold" />
        </div>
    </section>
</main>

@endsection
