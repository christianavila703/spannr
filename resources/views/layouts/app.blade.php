<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'spannr') }}</title>

    @stack('head')
    <script src="{{ mix('js/app.js') }}" defer></script>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/e87179c270.js" crossorigin="anonymous"></script>
    @livewireStyles
    <link rel="stylesheet" href="{{ mix('css/choices.css') }}" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js@9.0.1/public/assets/scripts/choices.min.js"></script>
</head>
<body>
    <div id="app">
        <nav id="primary-nav" class="navbar navbar-expand-lg navbar-dark bg-gradient-yellow-pink text-white align-items-center">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo-color.svg') }}" alt="spannr">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse align-items-center" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto align-items-center">
                        @can('view records')
                            <livewire:search />
                        @endcan
                        @can('view records')
                            <li class="nav-item">
                                <a href="{{ route('longevity-index') }}" class="nav-link">Longevity Stock Index</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbar-dropdown-records" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Records
                                </a>

                                <div class="dropdown-menu dropdown-menu-start" aria-labelledby="navbar-dropdown-records">
                                    <a class="dropdown-item" href="{{ route('organizations.index') }}">Organizations</a>
                                    <a class="dropdown-item" href="{{ route('people.index') }}">People</a>
                                    <a class="dropdown-item" href="{{ route('investors.index') }}">Investors</a>
                                    <a class="dropdown-item" href="{{ route('fundraising-rounds.index') }}">Fundraising Rounds</a>
                                    <a class="dropdown-item" href="{{ route('jobs.index') }}">Jobs</a>
                                    <a class="dropdown-item" href="{{ route('events.index') }}">Events</a>
                                </div>
                            </li>
                        @endcan

                        @can('edit records')
                            <li class="nav-item dropdown">
                                <a id="navbar-dropdown-add-new" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Add New
                                </a>

                                <div class="dropdown-menu dropdown-menu-start" aria-labelledby="navbar-dropdown-add-new">
                                    <a href="{{ route('organizations.create') }}" class="dropdown-item">Organization</a>
                                    <a href="{{ route('people.create') }}" class="dropdown-item">Person</a>
                                    <a href="{{ route('investors.create') }}" class="dropdown-item">Investor</a>
                                    <a href="{{ route('jobs.create') }}" class="dropdown-item">Job</a>
                                    <a href="{{ route('events.create') }}" class="dropdown-item">Event</a>
                                </div>
                            </li>
                        @endcan

                        @can('admin control')
                            <li class="nav-item dropdown">
                                <a id="navbar-dropdown-admin" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Admin
                                </a>

                                <div class="dropdown-menu dropdown-menu-start" aria-labelledby="navbar-dropdown-admin">
                                    <a href="{{ route('activity-log.index') }}" class="dropdown-item">Activity Log</a>
                                    <a href="/backup" class="dropdown-item">Backups</a>
                                    <a href="/log-viewer" class="dropdown-item">Logs</a>
                                </div>
                            </li>
                        @endcan
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('users.change-password') }}">
                                        Settings
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @livewireScripts
    @stack('end')
</body>
</html>
