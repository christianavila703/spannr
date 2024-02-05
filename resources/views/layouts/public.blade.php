<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('head')
    @isset($seo)
        {!! $seo->seo !!}
    @else
        @stack('seo')
    @endisset

    <title>{{ isset($pageTitle) ? $pageTitle : config('app.name', 'Spannr') }}</title>

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
