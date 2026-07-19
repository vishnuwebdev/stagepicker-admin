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
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    
    'firebase' => [
        'api_key' => 'AIzaSyB21YVALjKHU_Q3S76qT9vdH908esWbTJI',
        'auth_domain' => 'stage-picker.firebaseapp.com',
        'database_url' => 'database_url',
        'project_id' => 'stage-picker',
        'storage_bucket' => 'stage-picker.appspot.com',
        'messaging_sender_id' => '412983439746',
        'app_id' => '1:412983439746:web:16cc369397cefcfae55f9b',
        'measurement_id' => 'G-6TRJYRMVGM',
    ],

];
