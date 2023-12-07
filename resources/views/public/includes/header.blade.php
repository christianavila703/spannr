<div class="header-container sticky-top">
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
                    <div class="ms-auto d-flex align-items-center">
                        <div class="d-none d-md-block me-3">
                            <button class="btn text-uppercase text-white py-1" data-bs-toggle="modal" data-bs-target="#header-subscribe-modal">
                                <i class="fa-sharp fa-light fa-envelope me-1"></i>
                                <span>Subscribe</span>
                            </button>
                        </div>
                        <button class="hamburger hamburger--elastic" type="button" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvas-primary-menu" aria-controls="offcanvas-primary-menu">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    @yield('subheader')
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvas-primary-menu" aria-labelledby="offcanvas-primary-menu-label">
    <div class="offcanvas-header">
        <div class="offcanvas-title pt-3" id="offcanvas-primary-menu-label">
            <a class="navbar-brand" href="/">
                <img src="{{ Vite::asset('resources/images/logo-color.png') }}" alt="Spannr">
            </a>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        @include('public.includes.menu-primary')
    </div>
</div>
<div class="modal fade" id="header-subscribe-modal" tabindex="-1" aria-labelledby="header-subscribe-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-quaternary border-0">
                <div class="modal-title text-white" id="header-subscribe-modal-label">Sign Up For Our Newsletter</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-content mb-1">Weekly insights into the future of longevity</p>
                <p class="mb-3">Don't worry, we won't share your info.</p>
                <livewire:forms.newsletter-opt-in
                    form="{{ \App\Models\FormEntry::FORM_NEWSLETTER_HEADER }}"
                    formClass="header-subscribe-form"
                    successClass="text-start text-quintary h6 fw-bold"
                />
            </div>
        </div>
    </div>
</div>
