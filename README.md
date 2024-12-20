# WHMCS API
![GitHub release (latest by date)](https://img.shields.io/github/v/release/fintech-systems/whmcs-php-api) ![Tests](https://github.com/fintech-systems/whmcs-php-api/actions/workflows/tests.yml/badge.svg) ![GitHub](https://img.shields.io/github/license/eugenefvdm/whmcs-api) ![Downloads](https://img.shields.io/packagist/dt/eugenefvdm/whmcs-api)

A testable WHMCS API designed to run standalone or as part of a Laravel Application

Requirements:

- PHP 8.2
- WHMCS

# Installation

```bash
composer require eugenefvdm/whmcs-api
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Eugenefvdm\Whmcs\WhmcsServiceProvider" 
```

## Configuration Settings `whmcs.php`

```php
<?php

return [
    'url' => env('WHMCS_URL'),
    'api_identifier' => env('WHMCS_API_IDENTIFIER'),
    'api_secret' => env('WHMCS_API_SECRET'),
    'limitnum' => env('WHMCS_LIMITNUM', 10000),
    'debug' => env('WHMCS_DEBUG'),
];
```

`WHMCS_URL` should be without the trialing slash.

## WHMCS Setup

Allow the IP address of the system connecting to WHMCS:

```
Systems Settings => General => Security => API IP Access Restriction
```

Create API permissions:

```
System Settings => API credentials => Generate New API Credential
```

### Example API permissions required:

- addclient
- getclientsdetails
- getclientsdomains
- custom list of API calls you are developing

### Example, creating a new client with custom fields:

```php
test('it can create a new client in WHMCS', function () {
    $customfields = base64_encode(
        serialize(
            [
                8 => '50-100',
            ]
        )
    );

    $client = [
        'firstname' => $this->faker->firstName,
        'lastname' => $this->faker->lastName,
        'email' => $this->faker->email,
        'address1' => $this->faker->numberBetween(1, 100).' '.$this->faker->streetName,
        'address2' => $this->faker->secondaryAddress,
        'city' => $this->faker->city,
        'state' => $this->faker->state,
        'postcode' => $this->faker->postcode,
        'country' => config('whmcs.default_country'),
        'phonenumber' => $this->faker->phoneNumber,
        'password2' => bin2hex(random_bytes(8)),
        'customfields' => $customfields,
    ];

    $result = Whmcs::addClient($client);

    expect($result)->toHaveKey('result')
        ->and($result['result'] == 'success');
})->only();
```

### Custom API calls

WHMCS removed the ability to add custom API calls, but there is a hack to get it working again. 

An example of a custom API call would be to get a client by their phone number. Let's call this `getclientbyphonenumber`. At least two steps are required.

1. Create a custom API call
2. Inject the permission into the database

#### Example custom API call

Save the example code below here:
`includes/api/getclientbyphoneumber.php`

```php
<?php

use WHMCS\Database\Capsule;

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

try {
    $client = Capsule::table('tblclients')
        ->where("phonenumber", $_REQUEST['phonenumber'])
        ->first();

    if ($client) {
        $apiresults = [
            "result" => "success",
            "message" => "ok",
            'clientid' => $client->id,
        ];
    } else {
        $apiresults = [
            "result" => "error",
            "message" => "not found",
        ];
    }
} catch (Exception $e) {
    $apiresults = ["result" => "error", "message" => $e->getMessage()];
}
```

Next to use the custom API call `getclientbyphonenumber` you need to manually update `tblapi_roles` and add it there.

Also remember
to update it every time again you make a changes in the UI because the UI will *overwrite* the custom API call.

In the example below, the JSON in the database has been updated to include the new API call `getclientbyphonenumber`.

```json
{"addclient":1,"getclientsdetails":1,"getclientbyphonenumber":1}
```

If you haven't added the PHP file yet, you'll get `API Function Not Found`.

# Examples

## Framework Agnostic PHP

```php
<?php

use Eugenefvdm\WhmcsApi\WhmcsApi;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$server = [
    'url'            => env('WHMCS_URL'),
    'api_identifier' => env('WHMCS_API_IDENTIFIER'),
    'api_secret'     => env('WHMCS_API_SECRET'),
];

$api = new WhmcsApi($server);

$result = $api->getClients();
```

## Laravel

Publish the configuration file:

`php artisan vendor:publish --tag=whmcs-config`

# Changelog

See [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

# Features

## Set Server

Provides the ability to connect to a secondary WHMCS server away from the Facade initiation

```php
Whmcs::setServer([
    'url'            => env('WHMCS_URL2'),
    'api_secret'     => env('WHMCS_API_SECRET2'),
    'api_identifier' => env('WHMCS_API_IDENTIFIER2'),
])
```

Change Package

## Change Package

Framework Agnostic PHP:

```php
$newServiceId = 5;

$api = new WhmcsApi;
$api->changePackage($newServiceId);
```

Laravel App:

## Change server

Since we're using a Facade instantiated when it's called,
we need some other way to call the constructor when we're connecting to another server.

```php
public function setServer($server) {
        $this->url            = $server['url'];
        $this->api_identifier = $server['api_identifier'];
        $this->api_secret     = $server['api_secret'];
    }
```

```php
$newServiceId = 5;

WhmcsApi::changePackage($newServiceId);
```

Result:

A new package is applied to the service. If the package is linked to an API, the API will be called. 

# Testing

Before testing, please ensure you have copied over the `.env.example` file to `.env` as the tests use `dotenv`.

```bash
cp .env.example .env
```

## Debugging

Set `WHMCS_DEBUG=true` in your `.env` file to enable debugging. This will output to Spatie Ray if it's installed.

## Running the tests

`./vendor/bin/pest`

To test custom API actions, use a script such as the following:

`sh .scp updateclientaddon.php;./vendor/bin/pest`

The `.scp` file should have a copy command, e.g.:

```bash
#!/bin/bash
echo "Present Working Directory:"
pwd
echo "Copying $1 to WHMCS install directory"

cp includes/api/$1 ../whmcs/includes/api
echo "Done."
ls -la ../whmcs/includes/api/$1
```

## No errors on API actions but not working

API actions are difficult to troubleshoot if you don't observe the server log file.
Only some exceptions, e.g., model problems will be caught by the Try Catch block.
So help tail your server log file.
For example, if you're using Laravel's Valet's Nginx server, do this:

`tail -f ~/.valet/Log/nginx-error.log`

## Invalid IP 127.0.0.1

If you get `Invalid IP 127.0.0.1` that means you haven't allowed WHMCS API access from localhost. 

Navigate here: https://whmcs.test/admin/configgeneral.php and make sure you add `127.0.0.1` to API IP Access Restriction.

## Invalid or missing credentials

If you get `Invalid or missing credentials` that means you haven't added API roles and API credentials. Both are required before you can test. Also be sure to add them to your `.env` file:

```
WHMCS_API_IDENTIFIER=
WHMCS_API_SECRET=
```

## Invalid Permissions: API action "addclient" is not allowed

If you get `Invalid Permissions: API action "addclient" is not allowed` that means,
although you've added API roles and credentials, your roles are not set up properly for the API call.
Revisit roles and the requisite subsection and see where you have to click the checkbox to allow this API call.

## Storage folder examples

The `storage` folder has examples API responses, also used for caching during tests.

# Local Editing

For local editing, add this to `composer.json`:

```
"repositories" : [
        {
            "type": "path",
            "url": "../whmcs-api"
        }
    ],
```

Then in `require` section:

```
"eugenefvdm/whmcs-api": "dev-main",
```

Then do this to symlink:

```bash
composer require eugenefvdm/whmcs-api:dev-main
```

# License

MIT

# Author

hello (at) eugenefvdm.com <br>
https://eugenefvdm.com <br>
+27 82 309–6710 <br>
I am a Laravel, hosting, and WHMCS specialist.
Contact me any time for assistance.