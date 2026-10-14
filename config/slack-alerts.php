<?php

return [
    /*
     * The webhook URLs that we'll use to send a message to Slack. DEV-21: none is written in the code (a webhook is a secret).
     *
     * Every channel name below is used by SlackAlert::to('name') in the app. Each one reads its own env value if there is one
     * (SLACK_ALERTS_WEBHOOK_URL_<NAME in capitals>, for example SLACK_ALERTS_WEBHOOK_URL_WEBSITE), and otherwise the shared
     * SLACK_ALERTS_WEBHOOK_URL, so one address serves every channel today and channels can be split later by adding env values,
     * with no code change. An empty or missing value is fine: the package then sends nothing for that channel and does not throw.
     */
    'webhook_urls' => [
        'default' => env('SLACK_ALERTS_WEBHOOK_URL_DEFAULT') ?: env('SLACK_ALERTS_WEBHOOK_URL'),
        'index' => env('SLACK_ALERTS_WEBHOOK_URL_INDEX') ?: env('SLACK_ALERTS_WEBHOOK_URL'),
        'investing' => env('SLACK_ALERTS_WEBHOOK_URL_INVESTING') ?: env('SLACK_ALERTS_WEBHOOK_URL'),
        'jobs' => env('SLACK_ALERTS_WEBHOOK_URL_JOBS') ?: env('SLACK_ALERTS_WEBHOOK_URL'),
        'website' => env('SLACK_ALERTS_WEBHOOK_URL_WEBSITE') ?: env('SLACK_ALERTS_WEBHOOK_URL'),
        'test' => env('SLACK_ALERTS_WEBHOOK_URL_TEST') ?: env('SLACK_ALERTS_WEBHOOK_URL'),
        'marketplace' => env('SLACK_ALERTS_WEBHOOK_URL_MARKETPLACE') ?: env('SLACK_ALERTS_WEBHOOK_URL'),
    ],

    /*
     * This job will send the message to Slack. You can extend this
     * job to set timeouts, retries, etc...
     */
    'job' => Spatie\SlackAlerts\Jobs\SendToSlackChannelJob::class,
];
