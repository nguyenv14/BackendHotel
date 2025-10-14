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

    'facebook' => [
        'client_id' => '560404985530900',  //client face của bạn
        'client_secret' => '3a3f065bdf6d8dbe9f132384ae812f8a',  //client app service face của bạn
        'redirect' => 'https://nhuandeptraivanhanbro.doancoso2.laravel.vn/DoAnCoSo2/user/login-facebook/callback' //callback trả về
    ],

    'google' => [
        'client_id' => '230357011152-u1cchhog8fclnpmsaffnulm6nd34pj1r.apps.googleusercontent.com',
        'client_secret' => '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',
        'redirect' => 'https://nhuandeptraivanhanbro.doancoso2.laravel.vn/DoAnCoSo2/user/login-google/callback'
    ],

];
