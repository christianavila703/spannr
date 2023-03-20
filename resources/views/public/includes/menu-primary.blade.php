<li class="nav-item {{ (Route::currentRouteName() === 'public.content.about') ? 'current' : '' }}">
    <a class="nav-link" href="{{ route('public.content.about') }}">About</a>
</li>
<li class="nav-item {{ (Route::currentRouteName() === 'public.content.articles.index' OR Route::currentRouteName() === 'public.content.articles.show') ? 'current' : '' }}">
    <a class="nav-link" href="{{ route('public.content.articles.index') }}">Insights</a>
</li>
<li class="nav-item {{ (Route::currentRouteName() === 'public.content.longevity-index') ? 'current' : '' }}">
    <a class="nav-link" href="{{ route('public.content.longevity-index') }}">Index</a>
</li>
<li class="nav-item {{ (Route::currentRouteName() === 'public.content.ventures') ? 'current' : '' }}">
    <a class="nav-link" href="{{ route('public.content.ventures') }}">Investing</a>
</li>
<li class="nav-item {{ (Route::currentRouteName() === 'public.content.jobs.index') ? 'current' : '' }}">
    <a class="nav-link" href="{{ route('public.content.jobs.index') }}">Jobs</a>
</li>
<li class="nav-item {{ (Route::currentRouteName() === 'public.content.glossary.index') ? 'current' : '' }}">
    <a href="{{ route('public.content.glossary.index') }}" class="nav-link">Glossary</a>
</li>
{{--<li class="nav-item">--}}
{{--    <a class="nav-link" href="#">Data</a>--}}
{{--</li>--}}
{{--<li class="nav-item">--}}
{{--    <a class="nav-link" href="#">Jobs</a>--}}
{{--</li>--}}
