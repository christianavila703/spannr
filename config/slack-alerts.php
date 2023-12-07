<?php

return [
    /*
     * The webhook URLs that we'll use to send a message to Slack.
     */
    'webhook_urls' => [
        'default' => '', // #dev-updates
        'index' => '', // #index-notifications
        'investing' => '', // #investing
        'jobs' => '', // #jobs
        'website' => '', // #website
        'test' => '', // #test-notifications
        'marketplace' => '' // #marketplace
    ],

    /*
     * This job will send the message to Slack. You can extend this
     * job to set timeouts, retries, etc...
     */
    'job' => Spatie\SlackAlerts\Jobs\SendToSlackChannelJob::class,
];
