<div class="marketplace-join-banner py-3">
    <div class="container py-5">
        <div class="content">
            <img src="{{ Vite::asset('resources/images/spannr-logo-white.svg') }}" alt="Spannr" class="logo" width="150" height="36" loading="lazy">
            <div class="h1 title">Join the Longevity Marketplace</div>
            <p>The only marketplace dedicated to longevity seekers.</p>
            <div>
                <a href="{{ route('public.marketplace.join') }}" class="btn btn-lg btn-outline-light">List Your Business</a>
                <div class="small mt-1">Want more info? <a href="{{ route('public.content.contact') }}" class="text-white">Contact us</a>.</div>
            </div>
        </div>
        <div class="image">
            <img src="{{ Vite::asset('resources/images/marketplace-join.svg') }}" alt="Join the Spannr Marketplace" width="768" height="584" loading="lazy">
        </div>
    </div>
</div>
