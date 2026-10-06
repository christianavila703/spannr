<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('head')
    {{-- Laravel SEO: articles use seo($seoModel); CMS pages use $seo->seo; other routes may @push('seo') for legacy meta. Calling seo() here for all branches would duplicate tags on legacy views. --}}
    @if(isset($seo))
        {!! $seo->seo !!}
    @elseif(isset($seoModel))
        {!! seo()->for($seoModel) !!}
        @stack('seo-extra')
    @else
        @stack('seo')
    @endif

    @if(! isset($seoModel) && ! isset($seo))
        <title>@hasSection('title')@yield('title')@else{{ isset($pageTitle) ? $pageTitle : config('app.name', 'Spannr') }}@endif</title>
    @endif

    <link rel="preload" href="{{ Vite::asset('resources/fonts/LubalinGraphStd-Demi.woff2') }}" crossorigin="anonymous" as="font" type="font/woff2">
    @vite('resources/js/app.js')
    @livewireStyles
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-TW8JCMEBZF"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-TW8JCMEBZF');
    </script>
    <script defer data-domain="spannr.com" src="https://plausible.io/js/plausible.js"></script>
    <link rel="stylesheet" href="{{ Vite::asset('resources/sass/fontawesome.scss') }}" media="none" onload="if(media!=='all')media='all'">
</head>
<body>
    @if(!isset($showHeader) || $showHeader !== false)
        @include('public.includes.header')
    @endif

    <div class="public-main">
        @yield('content')
    </div>

    @if(!isset($showFooter) || $showFooter !== false)
        @include('public.includes.footer')
    @endif

    @livewireScripts
    @stack('end')

    <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
</body>
</html>
