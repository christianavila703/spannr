<div class="spannr-plus-hero bg-primary">
    <div class="background-overlay"></div>
    <div class="container">
        <div class="hero-image">
            <img src="{{ Vite::asset('resources/images/plus/hero-mobile.svg') }}" alt="Spannr+">
        </div>
        <div class="hero-content">
            <div class="logo-container">
                @include('public.includes._spannr-plus-logo')
            </div>
            <h1>Your Gateway <span class="d-md-block">to the World</span> <span class="d-md-block">of Longevity</span></h1>
            <p class="h4 text-uppercase text-white">$299/year</p>
            <div class="form-container">
                <livewire:public.plus.signup />
            </div>
        </div>
    </div>
</div>
