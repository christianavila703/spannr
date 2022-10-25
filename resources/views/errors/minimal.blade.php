<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('content') - {{ config('app.name', 'Spannr') }}</title>
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
    <script src="https://kit.fontawesome.com/e87179c270.js" crossorigin="anonymous"></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-TW8JCMEBZF"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-TW8JCMEBZF');
    </script>
    @livewireStyles
</head>
<body>
    @include('public.includes.header')

    <div class="public-error-container">
        <div class="container py-5 my-5 d-flex flex-column align-items-center justify-content-center">
            <h1 class="text-center">@yield('code') - @yield('message')</h1>
            <div class="max-width-800 mx-auto text-center">
                @yield('content')
            </div>
        </div>
    </div>

    @include('public.includes.footer')
    @livewireScripts
</body>
</html>
