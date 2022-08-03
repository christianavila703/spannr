<header class="header">
    <div class="logo">
        <a href="/">
            <img src="{{ asset('images/spannr-logo-white.svg') }}" alt="Spannr" class="logo">
        </a>
    </div>
    <nav id="primary-public-nav" class="d-flex align-items-center justify-content-center mt-2">
        <a href="{{ route('public.content.articles.index') }}">News</a>
        <span class="mx-2">&bull;</span>
        <a href="{{ route('public.content.longevity-index') }}">Index</a>
        <span class="mx-2">&bull;</span>
        <a href="{{ route('public.content.ventures') }}">Invest</a>
    </nav>
</header>
