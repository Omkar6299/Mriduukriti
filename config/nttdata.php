<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Atom Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file allows to easily switch between demo and live
    | environments by changing the 'environment' value and corresponding keys.
    |
    */

    'environment' => env('ATOM_ENV', 'demo'), // 'demo' or 'live'

    'demo' => [
        'login' => env('ATOM_DEMO_LOGIN', '445842'),
        'password' => env('ATOM_DEMO_PASSWORD', 'Test@123'),
        'prod_id' => env('ATOM_DEMO_PROD_ID', 'AIPAY'),
        'enc_request_key' => env('ATOM_DEMO_ENC_REQUEST_KEY', 'A4476C2062FFA58980DC8F79EB6A799E'),
        'dec_response_key' => env('ATOM_DEMO_DEC_RESPONSE_KEY', '75AEF0FA1B94B3C10D4F5B268F757F11'),
        'api_url' => env('ATOM_DEMO_API_URL', 'https://paynetzuat.atomtech.in/ots/aipay/auth'),
    ],

    'live' => [
        'login' => env('ATOM_LIVE_LOGIN', ''),
        'password' => env('ATOM_LIVE_PASSWORD', ''),
        'prod_id' => env('ATOM_LIVE_PROD_ID', ''),
        'enc_request_key' => env('ATOM_LIVE_ENC_REQUEST_KEY', ''),
        'dec_response_key' => env('ATOM_LIVE_DEC_RESPONSE_KEY', ''),
        'api_url' => env('ATOM_LIVE_API_URL', 'https://paynetz.atomtech.in/ots/aipay/auth'),
    ],
];

