<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'api_config' => [
        'api_key' => env('SUPA_API_KEY', ''),
        'url' => env('SUPA_URL', ''),
        'auth_email' => env('SUPA_AUTH_EMAIL', 'admin@admin.com'),
        'auth_password' => env('SUPA_AUTH_PASSWORD', ''),
    ],

];
