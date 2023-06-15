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
