<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'polygon' => [
        'key' => env('POLYGON_KEY'),
        'edge-id' => 'spannr',
        'edge-ip' => '144.202.43.148',
        'edge-user-agent' => 'spannr'
    ],

    'wordpress' => [
        'url' => env('WORDPRESS_URL'),
        'username' => env('WORDPRESS_USERNAME'),
        'password' => env('WORDPRESS_PASSWORD'),
    ],

    'mailchimp' => [
        'key' => env('MAILCHIMP_API_KEY'),
        'server' => env('MAILCHIMP_SERVER', 'us14')
    ],

    'stripe' => [
        'api_key' => env('STRIPE_API_KEY'),
        'job_posting_subscription_url' => env('STRIPE_JOBS_MONTHLY_SUBSCRIPTION_LINK'),
        'spannr_plus_subscription_url' => env('STRIPE_SPANNR_PLUS_SUBSCRIPTION_LINK'),
    ],

];
