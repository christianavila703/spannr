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
    <script src="{{ mix('js/app.js') }}" defer></script>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/e87179c270.js" crossorigin="anonymous"></script>
    @livewireStyles
    <!-- ADD GOOGLE ANALYTICS TAG -->
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
