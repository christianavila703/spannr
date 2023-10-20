<div class="spannr-plus-signup-cta">
    <div class="container">
        <div class="row pt-5">
            <div class="col-12 col-lg-6 pb-lg-4">
                <div class="logo-container">
                    @include('public.includes._spannr-plus-logo')
                </div>
                <h3 class="h1">Explore the World <span class="d-lg-block">of Longevity</span></h3>
                <p>Join Spannr+ today to gain access to the companies, investors, and people who are changing the world of human healthspan, lifespan, and longevity.</p>
                <div class="form-container">
                    <livewire:public.plus.signup />
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="cta-image">
                    <img src="{{ Vite::asset('resources/images/plus/signup-cta.svg') }}" alt="Spannr+">
                </div>
            </div>
        </div>
    </div>
</div>
