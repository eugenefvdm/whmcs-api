<?php

use Eugenefvdm\Whmcs\Facades\Whmcs;
use Eugenefvdm\Whmcs\Tests\Config;
use Eugenefvdm\Whmcs\Whmcs as WhmcsApi;
use Illuminate\Support\Facades\Http;

/**
 * The configuration variable to a shortcut to WHMCS credentials
 */
$config = new Config;

test('php pest is installed and operational', function () {
    expect(true)->toBeTrue();
});

// This will ensure you have a development server installed locally and is skipped by default.
test('it can access a test whmcs installation', function () {
    // If using Laravel Valet, https://whmcs.test won't have a legitimate certificate, so let's skip checking for that.
    $arrContextOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ];

    file_get_contents(env('WHMCS_URL'), false, stream_context_create($arrContextOptions));

    $this->assertEquals($http_response_header[0], 'HTTP/1.1 200 OK');
})->todo('Create a test WHMCS installation before enabling this automated test.');

test("adding a new user to WHMCS doesn't return an invalid IP error", function () use ($config) {
    $whmcs = new WhmcsApi($config->server);

    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            'result' => 'success',
            'clientid' => 1,
            'owner_id' => 1,
        ]),
    ]);

    $result = $whmcs->addUser([
        'password2' => 'password',
        'firstname' => 'First Name',
        'lastname' => 'Last Name',
        'email' => 'user@example.com',
        'address1' => '123 Hysteria Lane',
        'city' => 'Beverly Hills',
        'state' => 'California',
        'postcode' => '90210',
        'country' => 'US',
        'phonenumber' => '+27.66 245 4302',
    ]);

    expect($result)->toHaveKey('result', 'success')
        ->and($result)->toHaveKey('clientid')
        ->and($result)->toHaveKey('owner_id');
});

test('it will add an USA user with telephone number and dashes to the system for later testing', function () use ($config) {
    $whmcs = new WhmcsApi($config->server);

    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            'result' => 'success',
            'clientid' => 1,
            'owner_id' => 1,
        ]),
    ]);

    $result = $whmcs->addUser([
        'email' => 'user1@example.com',
        'password2' => 'password',
        'firstname' => 'First Name',
        'lastname' => 'Last Name',
        'address1' => '123 Penny Lane',
        'city' => 'Beverly Hills',
        'state' => 'California',
        'postcode' => '90210',
        'country' => 'US',
        'phonenumber' => '+1.408-555-1234',
    ]);

    expect($result)->toHaveKey('result', 'success')
        ->and($result)->toHaveKey('clientid')
        ->and($result)->toHaveKey('owner_id');
});

test('it can add a user to the billing system', function () use ($config) {
    $whmcs = new WhmcsApi($config->server);

    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            'result' => 'success',
            'clientid' => 1,
            'owner_id' => 1,
        ]),
    ]);

    $result = $whmcs->addUser([
        'email' => 'user23232@example.co.za',
        'password2' => 'password',
        'firstname' => 'First Name',
        'lastname' => 'Last Name',
        'address1' => '1 Gardens Street',
        'city' => 'Cape Town',
        'state' => 'Western Cape',
        'postcode' => '8001',
        'country' => 'ZA',
        'phonenumber' => '+27.662454302',
    ]);

    expect($result)->toHaveKey('result', 'success')
        ->and($result)->toHaveKey('clientid')
        ->and($result)->toHaveKey('owner_id');
});

test('it can find a South African user by telephone number in the billing system', function () use ($config) {
    $whmcs = new WhmcsApi($config->server);

    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            'result' => 'success',
            'message' => 'ok',
            'clientid' => 2,
        ]),
    ]);

    $result = $whmcs->getClientByPhoneNumber([
        'phonenumber' => '+27.82 309 6710',
    ]);

    expect($result)->toHaveKey('result', 'success')
        ->and($result)->toHaveKey('message', 'ok')
        ->and($result)->toHaveKey('clientid');
});

test('it can find a South African user by telephone number without spaces in the billing system', function () use ($config) {
    $whmcs = new WhmcsApi($config->server);

    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            'result' => 'success',
            'message' => 'ok',
            'clientid' => 3,
        ]),
    ]);

    $result = $whmcs->getClientByPhoneNumber([
        'phonenumber' => '+27.662454302',
    ]);

    expect($result)->toHaveKey('result', 'success');
});

test('it can find a South African user by telephone with spaces in the billing system', function () use ($config) {
    $whmcs = new WhmcsApi($config->server);

    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            'result' => 'success',
            'clientid' => 4,
        ]),
    ]);

    $result = $whmcs->getClientByPhoneNumber([
        'phonenumber' => '+27.66 245 4302',
    ]);

    expect($result)->toHaveKey('result', 'success');
});

test('it can find a USA user by telephone number in the billing system', function () use ($config) {
    $whmcs = new WhmcsApi($config->server);

    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            'result' => 'success',
            'message' => 'ok',
            'clientid' => 5,
        ]),
    ]);

    $result = $whmcs->getClientByPhoneNumber([
        'phonenumber' => '+1.408-555-1234',
    ]);

    expect($result)->toHaveKey('result', 'success');
});

test('it can connect to a WHMCS instance using the Laravel facade and pull a clients details', function () {
    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            'result' => 'success',
            'user' => 1,
            'clientid' => 1,
        ]),
    ]);

    $result = Whmcs::getClientsDetails([
        'clientid' => 1,
    ]);

    expect($result)->toHaveKey('result', 'success');
});

test("it can connect to a secondary WHMCS instance using a Laravel facade and retrieve a client's details", function () {
    Whmcs::setServer(
        [
            'url' => env('WHMCS_URL2'),
            'api_secret' => env('WHMCS_API_SECRET2'),
            'api_identifier' => env('WHMCS_API_IDENTIFIER2'),
        ]
    );

    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            'result' => 'success',
            'user' => 1,
            'clientid' => 1,
        ]),
    ]);

    $result = Whmcs::getClientsDetails([
        'clientid' => 1,
    ]);

    expect($result)->toHaveKey('result', 'success');
})->skip();

test('it can retrieve at least two domains', function () {
    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            'result' => 'success',
            'numreturned' => 2,
            'domains' => [
                'domain' => [
                    0 => [
                        'id' => 1,
                        'userid' => 1,
                        'orderid' => 1,
                        'regtype' => 'Register',
                        'domainname' => '1234.co.za',
                        'registrar' => 'email',
                    ],
                    1 => [
                        'id' => 2,
                        'userid' => 1,
                        'orderid' => 2,
                        'regtype' => 'Transfer',
                        'domainname' => 'example.co.za',
                        'registrar' => 'email',
                    ],
                ],
            ],
        ]),
    ]);

    $result = Whmcs::getClientsDomains();

    expect($result)->toHaveKey('result', 'success')
        ->and($result)->toHaveKey('numreturned', 2)
        ->and($result)->toHaveKey('domains');

    $count = count($result['domains']['domain']);

    expect($count)->toBe(2);
});

test('it retrieve registrars', function () {
    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            'status' => 'success',
            'registrars' => [
                0 => [
                    'module' => 'email',
                    'display_name' => 'Email Notifications',
                ],
                1 => [
                    'module' => 'enom',
                    'display_name' => 'Enom',
                ],

            ],
        ]),
    ]);

    $result = Whmcs::getRegistrars();

    expect($result)->toHaveKey('status', 'success')
        ->and($result)->toHaveKey(['registrars']['0']);
});

// sh .scp updateclientaddon.php;./vendor/bin/pest
test('it can use a modified UpdateClientAddon API action to inject new API actions', function () {
    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            'result' => 'success',
            'permissions' => '{...,"getclientbyphonenumber":1,"setregistrarsettingvalue":1}',
        ]),
    ]);

    $result = Whmcs::addApiCalls(
        [
            'getclientbyphonenumber',
            'setregistrarsettingvalue',
        ]
    );

    expect($result)->toHaveKey('result', 'success')
        ->and($result['permissions'])
        ->toContain('"getclientbyphonenumber":1,"setregistrarsettingvalue"');
});

// sh .scp setregistrarsettingvalue.php;./vendor/bin/pest
test("it can call a custom API action called 'setRegistrarSettingValue'", function () {
    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            'registrar' => 'email',
            'setting' => 'EmailAddress',
            'value' => 'test6@example.com',
        ]),
    ]);

    $result = Whmcs::setRegistrarSettingValue(
        'email',
        'EmailAddress',
        'test6@example.com'
    );

    expect($result)->toHaveKey('value', 'test6@example.com');
});

test("it can update a client's domain registrar", function () {
    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            'result' => 'success',
            'domainid' => 1,
        ]),
    ]);

    $result = Whmcs::updateClientDomain(
        1,
        ['registrar' => 'email']
    );

    expect($result)->toHaveKey('domainid', 1);
});

test('it can place a new order', function () use ($config) {
    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            'result' => 'success',
            'orderid' => 1,
            'serviceids' => '1,2',
            'addonids' => '',
            'domainids' => '',
            'invoiceid' => 10,
        ]),
    ]);

    $api = new WhmcsApi($config->server);

    $orderDetails = [
        'clientid' => 1,
        'paymentmethod' => 'mailin',
        'pid' => [1, 1],
    ];

    $result = $api->addOrder($orderDetails);

    expect($result)
        ->toHaveKey('result', 'success')
        ->toHaveKeys(['serviceids', 'invoiceid']);
});
