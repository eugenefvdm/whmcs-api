<?php

return [
    'url' => env('WHMCS_URL'),
    'admin_url' => env('WHMCS_ADMIN_URL'),
    'api_identifier' => env('WHMCS_API_IDENTIFIER'),
    'api_secret' => env('WHMCS_API_SECRET'),
    'debug' => env('WHMCS_DEBUG'),
    'limitnum' => env('WHMCS_LIMITNUM', 10000),
    'default_payment_method' => env('WHMCS_DEFAULT_PAYMENT_METHOD', 'mailin'),
];
