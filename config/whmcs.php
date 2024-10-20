<?php

return [
    'url' => env('WHMCS_URL'),
    'admin_url' => env('WHMCS_ADMIN_URL'),
    'api_identifier' => env('WHMCS_API_IDENTIFIER'),
    'api_secret' => env('WHMCS_API_SECRET'),
    'limitnum' => env('WHMCS_LIMITNUM', 10000),
    'debug' => env('WHMCS_DEBUG'),
];
