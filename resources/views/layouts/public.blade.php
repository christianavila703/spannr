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
    <title>{{ config('app.name', 'Spannr') }}</title>
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
    <script src="https://kit.fontawesome.com/e87179c270.js" crossorigin="anonymous"></script>
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
    @includeUnless($showHeader === false, 'public.includes.header')

    <div class="public-main">
        @yield('content')
    </div>

    @includeUnless($showFooter === false, 'public.includes.footer')

    @livewireScripts
    @stack('end')
</body>
</html>
