<?php

namespace Eugenefvdm\Whmcs\Tests;

use Exception;
use Symfony\Component\Dotenv\Dotenv;

class Config
{
    public $server;

    public function __construct()
    {
        $this->loadDotEnv();

        $this->server = [
            'url' => env('WHMCS_URL'),
            'api_identifier' => env('WHMCS_API_IDENTIFIER'),
            'api_secret' => env('WHMCS_API_SECRET'),
        ];
    }

    public function loadDotEnv()
    {
        $dotenv = new Dotenv();

        try {
            $dotenv->load(__DIR__.'/../.env');
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
