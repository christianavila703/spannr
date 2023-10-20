<li class="nav-item {{ ($currentNavItem === 'about') ? 'current' : '' }}">
    <a class="nav-link" href="{{ route('public.content.about') }}">About</a>
</li>
<li class="nav-item dropdown {{ ($currentNavItem === 'insights') ? 'current' : '' }}">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Insights
    </a>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="{{ route('public.content.longevity-guide.index') }}">Longevity 101</a>
            <a class="dropdown-item" href="{{ route('public.content.reports.longevity-report-2022') }}">Longevity Funding Report</a>
            <a class="dropdown-item" href="{{ route('public.content.articles.index') }}">All Articles</a>
        </li>
    </ul>
</li>
<li class="nav-item dropdown {{ ($currentNavItem === 'data') ? 'current' : '' }}">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Data
    </a>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="{{ route('public.content.organizations.index') }}">Companies</a>
            <a class="dropdown-item" href="{{ route('public.content.investors.index') }}">Investors</a>
            <a class="dropdown-item" href="{{ route('public.content.jobs.index') }}">Jobs</a>
            <a class="dropdown-item" href="{{ route('public.content.glossary.index') }}">Glossary</a>
        </li>
    </ul>
</li>
<li class="nav-item dropdown {{ ($currentNavItem === 'investing') ? 'current' : '' }}">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Investing
    </a>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="{{ route('public.content.longevity-index') }}">Index</a>
            <a class="dropdown-item" href="{{ route('public.content.ventures') }}">Ventures</a>
        </li>
    </ul>
</li>
@if(auth()->check())
    @if(auth()->user()->isSubscriptionActive())
        <li class="nav-item dropdown {{ ($currentNavItem === 'panel') ? 'current' : '' }}">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Spannr+
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="{{ route('panel.index') }}">Dashboard</a>
                    <a class="dropdown-item" href="{{ route('panel.organizations.index') }}">Organizations</a>
                    <a class="dropdown-item" href="{{ route('panel.investors.index') }}">Investors</a>
                    <a class="dropdown-item" href="{{ route('panel.people.index') }}">People</a>
                    <a class="dropdown-item" href="{{ route('panel.jobs.index') }}">Jobs</a>
                    <a class="dropdown-item" href="{{ route('panel.events.index') }}">Events</a>
                    <a class="dropdown-item" href="{{ route('panel.fundraising-rounds.index') }}">Fundraising Rounds</a>
                    <a class="dropdown-item" href="{{ route('panel.locations.index') }}">Locations</a>
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </li>
    @else
        <li class="nav-item dropdown {{ ($currentNavItem === 'panel') ? 'current' : '' }}">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Spannr+
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="{{ route('panel.index') }}">Subscribe</a>
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </li>
    @endif
@else
    <li class="nav-item {{ ($currentNavItem === 'panel') ? 'current' : '' }}">
        <a class="nav-link" href="{{ route('panel.index') }}" role="button">Spannr+</a>
    </li>
@endif
