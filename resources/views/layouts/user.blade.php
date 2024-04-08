<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'spannr') }}</title>

    @vite('resources/js/app.js')
    <link rel="stylesheet" href="{{ Vite::asset('resources/sass/fontawesome.scss') }}" media="none" onload="if(media!=='all')media='all'">
    @livewireStyles
    @stack('head')
</head>
<body>
    <div id="spannr-app">

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
                                    <a href="{{ route('users.dashboard') }}" class="btn text-uppercase text-white py-1">
                                        <i class="fa-sharp fa-light fa-home me-1"></i>
                                        <span>{{ \Illuminate\Support\Facades\Auth::user()->name }}</span>
                                    </a>
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

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @livewireScripts
    @stack('end')
</body>
</html>
