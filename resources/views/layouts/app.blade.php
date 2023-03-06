<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'spannr') }}</title>

    @stack('head')
    @vite('resources/js/app.js')
    @vite('resources/sass/app.scss')
    <script src="https://kit.fontawesome.com/e87179c270.js" crossorigin="anonymous"></script>
    @livewireStyles
    @vite('resources/sass/choices.scss')
    @vite('resources/sass/admin.scss')
    <script src="https://cdn.jsdelivr.net/npm/choices.js@9.0.1/public/assets/scripts/choices.min.js"></script>
</head>
<body>
    <div id="spannr-app">
        <nav id="primary-nav" class="navbar navbar-expand-lg navbar-dark bg-gradient-yellow-pink text-white align-items-center">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ Vite::asset('resources/images/logo-color.svg') }}" alt="spannr">
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
                                <a href="{{ route('admin.longevity-index') }}" class="nav-link"><span class="d-lg-none d-xl-inline-block">Longevity Stock</span> Index</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbar-dropdown-records" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Records
                                </a>

                                <div class="dropdown-menu dropdown-menu-start" aria-labelledby="navbar-dropdown-records">
                                    <a class="dropdown-item" href="{{ route('admin.organizations.index') }}">Organizations</a>
                                    <a class="dropdown-item" href="{{ route('admin.people.index') }}">People</a>
                                    <a class="dropdown-item" href="{{ route('admin.investors.index') }}">Investors</a>
                                    <a class="dropdown-item" href="{{ route('admin.fundraising-rounds.index') }}">Fundraising Rounds</a>
                                    <a class="dropdown-item" href="{{ route('admin.jobs.index') }}">Jobs</a>
                                    <a class="dropdown-item" href="{{ route('admin.events.index') }}">Events</a>
                                    <a class="dropdown-item" href="{{ route('admin.content.index') }}">Content</a>
                                    <a class="dropdown-item" href="{{ route('admin.form-entries.index') }}">Form Entries</a>
                                    <a class="dropdown-item" href="{{ route('admin.tags.index') }}">Tags</a>
                                    <a class="dropdown-item" href="{{ route('admin.articles.index') }}">Articles</a>
                                    <a class="dropdown-item" href="{{ route('admin.glossary.index') }}">Glossary</a>
                                    <a class="dropdown-item" href="{{ route('admin.articles.sync') }}">Sync WordPress</a>
                                    <a class="dropdown-item" href="{{ route('admin.entity-duplicate.index') }}">Duplicate Merge</a>
                                    @can('review records')
                                        <a class="dropdown-item" href="{{ route('admin.entity-reviews.index') }}">Review / Audit</a>
                                    @endcan
                                </div>
                            </li>
                        @endcan

                        @can('edit records')
                            <li class="nav-item dropdown">
                                <a id="navbar-dropdown-add-new" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Add New
                                </a>

                                <div class="dropdown-menu dropdown-menu-start" aria-labelledby="navbar-dropdown-add-new">
                                    <a href="{{ route('admin.organizations.create') }}" class="dropdown-item">Organization</a>
                                    <a href="{{ route('admin.people.create') }}" class="dropdown-item">Person</a>
                                    <a href="{{ route('admin.investors.create') }}" class="dropdown-item">Investor</a>
                                    <a href="{{ route('admin.jobs.create') }}" class="dropdown-item">Job</a>
                                    <a href="{{ route('admin.events.create') }}" class="dropdown-item">Event</a>
                                    <a href="{{ route('admin.content.create') }}" class="dropdown-item">Content</a>
                                    <a href="{{ route('admin.articles.create') }}" class="dropdown-item">Article</a>
                                    <a href="{{ route('admin.glossary.create') }}" class="dropdown-item">Glossary Item</a>
                                </div>
                            </li>
                        @endcan

                        @can('edit articles')
                            <li class="nav-item dropdown">
                                <a id="navbar-dropdown-records" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Articles
                                </a>

                                <div class="dropdown-menu dropdown-menu-start" aria-labelledby="navbar-dropdown-records">
                                    <a class="dropdown-item" href="{{ route('admin.articles.index') }}">Articles List</a>
                                    <a class="dropdown-item" href="{{ route('admin.articles.create') }}">New Article</a>
                                </div>
                            </li>
                        @endcan

                        @can('admin control')
                            <li class="nav-item dropdown">
                                <a id="navbar-dropdown-admin" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Admin
                                </a>

                                <div class="dropdown-menu dropdown-menu-start" aria-labelledby="navbar-dropdown-admin">
                                    <a href="{{ route('admin.activity-log.index') }}" class="dropdown-item">Activity Log</a>
                                    <a href="/admin/backup" class="dropdown-item">Backups</a>
                                    <a href="/log-viewer" class="dropdown-item">Logs</a>
                                    <a href="{{ route('admin.users.index') }}" class="dropdown-item">Users</a>
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
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fa-sharp fa-solid fa-user-bounty-hunter me-2"></i><span class="d-lg-none d-xl-inline-block">{{ Auth::user()->name }}</span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('users.settings') }}">
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
