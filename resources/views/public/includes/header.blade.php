<header class="header public-header @isset($headerClass){{ $headerClass }}@endisset">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                @if((Route::currentRouteName() === 'public.content.ventures'))
                    <a class="navbar-brand" href="/">
                        <img src="{{ Vite::asset('resources/images/spannr-ventures-logo-white.png') }}" alt="Spannr Ventures">
                    </a>
                @else
                    <a class="navbar-brand" href="/">
                        <img src="{{ Vite::asset('resources/images/spannr-logo-white.svg') }}" alt="Spannr" class="logo">
                    </a>
                @endif
                <button class="hamburger hamburger--elastic d-lg-none" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation menu">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto me-0 mt-2 mt-lg-0 text-start">
                        @include('public.includes.menu-primary')
                        <x-nav.contact-link />
                        <x-nav.admin-link />
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>
