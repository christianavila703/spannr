<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
         Horizon::routeMailNotificationsTo('sydney@spannr.com');

        // DEV-20: the Slack webhook is a secret, so it lives in the environment (HORIZON_SLACK_WEBHOOK_URL), not in the code.
        // When it is empty no Slack route is set, and Horizon simply sends no Slack notifications.
        $slackWebhook = config('services.slack.horizon_webhook');

        if (filled($slackWebhook)) {
            Horizon::routeSlackNotificationsTo($slackWebhook, '#dev-updates');
        }

        // Horizon::night();
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewHorizon', function ($user) {
            return $user->hasRole('Super Admin');
        });
    }
}
