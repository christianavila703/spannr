<nav id="primary-nav" class="public">
    <div class="py-3 has-collapse-arrows-end border-bottom">
        <button class="btn h6 nav-heading-item {{ ($currentNavItem === 'marketplace') ? 'current' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#nav-item-marketplace"
                aria-expanded="{{ ($currentNavItem === 'marketplace') ? 'true' : 'false' }}" aria-controls="nav-item-marketplace">
            Marketplace
        </button>
        <div class="collapse {{ ($currentNavItem === 'marketplace') ? 'show' : '' }}" id="nav-item-marketplace">
            <ul>
                <li><a href="{{ route('public.marketplace.cold-plunge') }}">Cold Plunge</a></li>
                <li><a href="{{ route('public.marketplace.red-light-therapy') }}">Red Light Therapy</a></li>
                <li><a href="{{ route('public.marketplace.sauna') }}">Sauna</a></li>
                <li><a href="{{ route('public.marketplace.index') }}">All Listings</a></li>
            </ul>
        </div>
    </div>
    <div class="py-3 has-collapse-arrows-end border-bottom">
        <button class="btn h6 nav-heading-item {{ ($currentNavItem === 'insights') ? 'current' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#nav-item-insights"
                aria-expanded="{{ ($currentNavItem === 'insights') ? 'true' : 'false' }}" aria-controls="nav-item-insights">
            Insights
        </button>
        <div class="collapse {{ ($currentNavItem === 'insights') ? 'show' : '' }}" id="nav-item-insights">
            <ul>
                <li><a href="{{ route('public.content.longevity-guide.index') }}">Longevity 101</a></li>
                <li><a href="{{ route('public.content.reports.longevity-report-2022') }}">Longevity Funding Report</a></li>
                <li><a href="{{ route('public.content.articles.index') }}">All Longevity Articles</a></li>
            </ul>
        </div>
    </div>
    <div class="py-3 has-collapse-arrows-end border-bottom">
        <button class="btn h6 nav-heading-item {{ ($currentNavItem === 'data') ? 'current' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#nav-item-data"
                aria-expanded="{{ ($currentNavItem === 'data') ? 'true' : 'false' }}" aria-controls="nav-item-data">
            Data
        </button>
        <div class="collapse {{ ($currentNavItem === 'data') ? 'show' : '' }}" id="nav-item-data">
            <ul>
                <li><a href="{{ route('public.content.organizations.index') }}">Companies</a></li>
                <li><a href="{{ route('public.content.investors.index') }}">Investors</a></li>
                <li><a href="{{ route('public.content.glossary.index') }}">Glossary</a></li>
                <li><a href="{{ route('public.content.longevity-index') }}">Stock Index</a></li>
                <li><a href="{{ route('public.content.ventures') }}">Ventures</a></li>
            </ul>
        </div>
    </div>

    @if(auth()->check())
        @if(auth()->user()->isSubscriptionActive())
            <div class="py-3 has-collapse-arrows-end border-bottom">
                <button class="btn h6 nav-heading-item {{ ($currentNavItem === 'panel') ? 'current' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#nav-item-spannr-plus"
                        aria-expanded="{{ ($currentNavItem === 'panel') ? 'true' : 'false' }}" aria-controls="nav-item-spannr-plus">
                    Spannr+
                </button>
                <div class="collapse {{ ($currentNavItem === 'panel') ? 'show' : '' }}" id="nav-item-spannr-plus">
                    <ul>
                        <li><a href="{{ route('panel.index') }}">Dashboard</a></li>
                        <li><a href="{{ route('panel.organizations.index') }}">Organizations</a></li>
                        <li><a href="{{ route('panel.investors.index') }}">Investors</a></li>
                        <li><a href="{{ route('panel.people.index') }}">People</a></li>
                        <li><a href="{{ route('panel.jobs.index') }}">Jobs</a></li>
                        <li><a href="{{ route('panel.events.index') }}">Events</a></li>
                        <li><a href="{{ route('panel.fundraising-rounds.index') }}">Fundraising Rounds</a></li>
                        <li><a href="{{ route('panel.locations.index') }}">Locations</a></li>
                    </ul>
                </div>
            </div>
        @else
            <div class="py-3 border-bottom">
                <a href="{{ route('panel.index') }}" class="nav-heading-item btn w-100 text-start h6 {{ ($currentNavItem === 'panel') ? 'current' : '' }}">Spannr+</a>
                <p class="m-0">Your gateway to the world of longevity</p>
            </div>
        @endif
    @else
        <div class="py-3 border-bottom">
            <a href="{{ route('panel.index') }}" class="nav-heading-item btn w-100 text-start h6 {{ ($currentNavItem === 'panel') ? 'current' : '' }}">Spannr+</a>
            <p class="m-0">Your gateway to the world of longevity</p>
        </div>
    @endif

    @can('manage own jobs')
        <div class="py-3 border-bottom">
            <a href="{{ route('public.content.jobs.index') }}" class="nav-heading-item btn w-100 text-start h6 {{ ($currentNavItem === 'jobs') ? 'current' : '' }}">Longevity Jobs</a>
            <ul class="other-nav-items">
                <li class="nav-item">
                    <a href="{{ route('users.jobs.index') }}">My Job Listings</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('public.content.jobs.submit') }}">List New Job</a>
                </li>
            </ul>
        </div>
    @else
        <div class="py-3 border-bottom">
            <a href="{{ route('public.content.jobs.index') }}" class="nav-heading-item btn w-100 text-start h6 {{ ($currentNavItem === 'jobs') ? 'current' : '' }}">Longevity Jobs</a>
            <p class="m-0">Find a job in longevity / Find candidates to hire</p>
        </div>
    @endcan

    @guest
        <div class="py-4 border-bottom">
            <p class="h6">Get the Spannr Newsletter</p>
            <p class="my-2">Weekly insights into the future of longevity</p>
            <div class="mailchimp-form">
                <livewire:forms.newsletter-opt-in
                    form="{{ \App\Models\FormEntry::FORM_NEWSLETTER_HOMEPAGE_HERO }}"
                    successClass="text-start text-quintary fw-bold"
                    uniquePrefix="menu-primary"
                />
            </div>
        </div>
    @endguest

    <div class="py-3">
        <ul class="other-nav-items">
            <x-nav.admin-link />
            @auth
                <li class="nav-item">
                    <a href="{{ route('users.dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                </li>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            @else
                <li class="nav-item">
                    <a href="{{ route('login') }}" class="nav-link">Login</a>
                </li>
            @endauth
            <li class="nav-item">
                <a href="{{ route('public.content.about') }}" class="nav-link">About Us</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('public.content.terms') }}" class="nav-link">Terms of Use</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('public.content.privacy') }}" class="nav-link">Privacy Policy</a>
            </li>
            <x-nav.contact-link />
        </ul>
    </div>
</nav>
