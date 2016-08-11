<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'linkedin' => [
        'client_id' => '81qialyoutao21',
        'client_secret' => '3HVaN2fjiCqvRtcp',
        'redirect' => 'http://local.laravel.com/callback?type=linkedin',
    ],

    'twitter' => [
        'client_id' => 'cqpLUGFTLnUI8IqastrAuMA8H',
        'client_secret' => '6auOmkIxi43kCu8yUSeQlxXxVVrtuPyIBuQoBjsIgofLYELero',
        'redirect' => 'http://local.laravel.com/callback?type=twitter',
    ],

];
