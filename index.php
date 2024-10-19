<?php

use Eugenefvdm\WhmcsApi\WhmcsApi;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$server = [
    'url' => env('WHMCS_URL'),
    'api_identifier' => env('WHMCS_API_IDENTIFIER'),
    'api_secret' => env('WHMCS_API_SECRET'),
];

$mode = env('WHMCS_API_MODE');

$api = new WhmcsApi($server, $mode);

$result = $api->getClients();

//ray($result);
