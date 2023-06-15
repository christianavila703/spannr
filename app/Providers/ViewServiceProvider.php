<?php
namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        View::composer('public.includes.menu-primary', function ($view) {
            $currentRoute = Route::currentRouteName();

            $currentNavItem = match($currentRoute) {
                'public.content.about' => 'about',
                'public.content.articles.index', 'public.content.articles.show', 'public.content.reports.longevity-report-2022', 'public.content.longevity-guide.index' => 'insights',
                'public.content.organizations.index', 'public.content.jobs.index', 'public.content.glossary.index' => 'data',
                'public.content.longevity-index', 'public.content.ventures' => 'investing',
                default => null
            };

            View::share('currentNavItem', $currentNavItem);
        });
    }
}
