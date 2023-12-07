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
                'public.content.about'                              => 'about',
                'public.content.articles.index',
                'public.content.articles.show',
                'public.content.reports.longevity-report-2022',
                'public.content.longevity-guide.index'              => 'insights',
                'public.content.organizations.index',
                'public.content.investors.index',
                'public.content.glossary.index'                     => 'data',
                'public.content.longevity-index',
                'public.content.ventures'                           => 'investing',
                'public.marketplace.index',
                'public.marketplace.show',
                'public.marketplace.cold-plunge',
                'public.marketplace.red-light-therapy',
                'public.marketplace.sauna'                          => 'marketplace',
                'public.content.jobs.index',
                'public.content.jobs.show',
                'public.content.jobs.apply'                         => 'jobs',
                'panel.index',
                'panel.organizations.index',
                'panel.organizations.show',
                'panel.investors.index',
                'panel.investors.show',
                'panel.people.index',
                'panel.people.show',
                'panel.jobs.index',
                'panel.jobs.show',
                'panel.events.index',
                'panel.events.show',
                'panel.fundraising-rounds.index',
                'panel.fundraising-rounds.show',
                'panel.locations.index',
                'panel.locations.show'                              => 'panel',
                default => null
            };

            View::share('currentNavItem', $currentNavItem);
        });
    }
}
