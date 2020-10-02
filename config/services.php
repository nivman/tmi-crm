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
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'enabled' => env('STRIPE_ENABLED', false),
        'key'     => env('STRIPE_KEY'),
        'secret'  => env('STRIPE_SECRET'),
    ],

    'paypal' => [
        'enabled' => env('PAYPAL_ENABLED', false),
        'id'      => env('PAYPAL_CLIENT_ID'),
        'secret'  => env('PAYPAL_SECRET'),
    ],

    'authorize' => [
        'enabled'         => env('AUTHORIZE_ENABLED', false),
        'login'           => env('AUTHORIZE_LOGIN'),
        'transaction_key' => env('AUTHORIZE_TRANSACTION_KEY'),
    ],
];
