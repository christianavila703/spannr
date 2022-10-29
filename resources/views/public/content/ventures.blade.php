@extends('layouts.public')

@section('content')
<main class="page-ventures">
    <section class="ventures-hero-container">
        <div class="container">
            <div class="content">
                <img src="{{ Vite::asset('resources/images/spannr-ventures-logo-white.png') }}" alt="spannr ventures" class="logo">
                <div>
                    <p>Across the globe, scientists are working on ways to extend healthy human lifespans beyond what we know. Significant advances have attracted serious investment and the force of this movement is changing the way we look at life.</p>
                    <h1>Invest in the future of humanity.</h1>
                    <a href="#signup" class="btn btn-rounded btn-outline">Sign Up</a>
                </div>
            </div>
            <div class="image-container">
                <img src="{{ Vite::asset('resources/images/ventures/ventures-hero.png') }}" alt="Invest in the future of humanity">
            </div>
        </div>
    </section>

    <section class="ventures-about-container">
        <div class="container">
            <div class="content">
                <h2 class="h1">The spannr <span>syndicate</span></h2>
                <p>The spannr syndicate gives you direct, exclusive access to highly-curated longevity investments alongside
a number of like-minded investors. Through our experience-led, data-driven investment selection process,
you can be confident that the deals you see are some of the best opportunities in the industry.</p>
            </div>
            <div class="image-container">
                <img src="{{ Vite::asset('resources/images/ventures/network.png') }}" alt="The spannr syndicate">
            </div>
        </div>
    </section>

    <section class="ventures-reasons-container">
        <div class="container text-center">
            <h2 class="h1">Become a spannr syndicate member in minutes and get exclusive access to:</h2>
            <div class="row heading-container">
                <div class="col-4">
                    <h3 class="reason-title">Our Curated Investments</h3>
                </div>
                <div class="col-4">
                    <h3 class="reason-title">Our Investment Expertise</h3>
                </div>
                <div class="col-4">
                    <h3 class="reason-title">Our Platform</h3>
                </div>
            </div>
            <div class="row icon-container">
                <div class="col-4">
                    <img src="{{ Vite::asset('resources/images/ventures/icon-investments.png') }}" alt="Our curated investments" class="icon">
                </div>
                <div class="col-4">
                    <img src="{{ Vite::asset('resources/images/ventures/icon-expertise.png') }}" alt="Our investment expertise" class="icon">
                </div>
                <div class="col-4">
                    <img src="{{ Vite::asset('resources/images/ventures/icon-platform.png') }}" alt="Our platform" class="icon">
                </div>
            </div>
            <div class="row arrow-container">
                <div class="col-4">
                    <img src="{{ Vite::asset('resources/images/ventures/arrow-mobile.png') }}" alt="" class="arrow mobile">
                    <img src="{{ Vite::asset('resources/images/ventures/arrow-desktop.png') }}" alt="" class="arrow desktop">
                </div>
                <div class="col-4">
                    <img src="{{ Vite::asset('resources/images/ventures/arrow-mobile.png') }}" alt="" class="arrow mobile">
                    <img src="{{ Vite::asset('resources/images/ventures/arrow-desktop.png') }}" alt="" class="arrow desktop">
                </div>
                <div class="col-4">
                    <img src="{{ Vite::asset('resources/images/ventures/arrow-mobile.png') }}" alt="" class="arrow mobile">
                    <img src="{{ Vite::asset('resources/images/ventures/arrow-desktop.png') }}" alt="" class="arrow desktop">
                </div>
            </div>
        </div>
    </section>

    <section class="ventures-investments-container">
        <div class="container">
            <h2 class="h1">Some of the companies our team has invested in or advised</h2>
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
                    <img src="{{ Vite::asset('resources/images/ventures/logos/endiatix.png') }}" alt="Endiatix">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/aria.png') }}" alt="ARIA Pharmaceuticals">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/rain.png') }}" alt="RAIN Neuromorphics">
                </div>
                <div class="logo">
                    <img src="{{ Vite::asset('resources/images/ventures/logos/gerostate-alpha.png') }}" alt="gerostate-alpha">
                </div>
            </div>
        </div>
    </section>

    <section id="signup" class="ventures-form-container">
        <div class="container">
            <h3 class="h1">Join us to learn more</h3>
            <livewire:forms.investor-opt-in formType="horizontal" formClass="ventures-optin-form" successClass="lead fw-bold" />
        </div>
        <div class="container copyright-container pt-5">
            <a href="/">
                <img src="{{ Vite::asset('resources/images/spannr-logo-tagline.png') }}" alt="spannr" class="logo">
            </a>
            <p>
                Copyright &copy; <?php echo date('Y'); ?> Spannr
            </p>
        </div>
    </section>
</main>

@endsection
