<?php

namespace Eugenefvdm\Whmcs;

use Eugenefvdm\Whmcs\Contracts\BillingProvider;
use Exception;
use Illuminate\Support\Facades\Http;

class Whmcs implements BillingProvider
{
    private string $url;

    private string $api_identifier;

    private string $api_secret;

    private array $mergeData;

    public function __construct($client)
    {
        $this->url = $client['url'];
        $this->api_identifier = $client['api_identifier'];
        $this->api_secret = $client['api_secret'];

        $this->throwExceptionIfUrlNotPresent();

        $this->mergeData = [
            'limitstart' => 0,
            'limitnum' => config('whmcs.limitnum'),
        ];
    }

    /**
     * Accept order
     *
     * https://developers.whmcs.com/api-reference/acceptorder/
     *
     * @return array|mixed
     *
     * @throws Exception
     */
    public function acceptOrder($data): mixed
    {
        return $this->call('AcceptOrder', $data);
    }

    /**
     * Add client
     *
     * https://developers.whmcs.com/api-reference/addclient/
     */
    public function addClient($data)
    {
        return $this->call('AddClient', $data);
    }

    /**
     * Custom: Add api calls
     *
     * Make provision for custom api calls
     */
    public function addApiCalls($array)
    {
        return $this->call('UpdateClientAddon', [
            'apis' => $array, ]
        );
    }

    /**
     * Add user
     *
     * https://developers.whmcs.com/api-reference/adduser/
     */
    public function addUser($data)
    {
        return $this->call('AddUser', $data);
    }

    /**
     * Add order
     *
     * Required fields: action, clientid, paymentmethod
     *
     * https://developers.whmcs.com/api-reference/addorder/
     */
    public function addOrder($data)
    {
        return $this->call('AddOrder', $data);
    }

    /**
     * Change plan
     *
     * Alias for module change package
     */
    public function changePlan($serviceid)
    {
        return $this->moduleChangePackage($serviceid);
    }

    /**
     * Decrypt password.
     *
     * https://developers.whmcs.com/api-reference/decryptpassword/
     * https://developers.whmcs.com/api/authentication/
     */
    public function decryptPassword($userIdentifier, $secret, $password2)
    {
        $data = [
            'username' => $userIdentifier,
            'password' => $secret,
            'password2' => $password2,
        ];

        return $this->call('DecryptPassword', $data);
    }

    /**
     * Domain transfer.
     *
     * https://developers.whmcs.com/api-reference/domaintransfer/
     */
    public function domainTransfer($domainid)
    {
        return $this->call('DomainTransfer', [
            'domainid' => $domainid,
        ]);
    }

    /**
     * Get clients
     *
     * https://developers.whmcs.com/api-reference/getclients/
     *
     * See the URL for the limitation of only seeing a very small subset of information
     */
    public function getClients($data = [])
    {
        return $this->call('GetClients', $data);
    }

    /**
     * Get clients details
     *
     * https://developers.whmcs.com/api-reference/getclientsdetails/
     *
     * Can take either $clientid or $email
     *
     * From the WHMCS reference:
     *  Note this function returns the client information in the top level array.
     *  This information is deprecated and may be removed in a future version of WHMCS.
     */
    public function getClientsDetails($client)
    {
        $action = 'GetClientsDetails';

        $data = [];

        if (isset($client['email'])) {
            $data = array_merge($data, ['email' => $client['email']]);
        }

        if (isset($client['clientid'])) {
            $data = array_merge($data, ['clientid' => $client['clientid']]);
        }

        return $this->call($action, $data);
    }

    /**
     * Get client by phone number
     *
     * Custom API 'getclientbyphonenumber.php'
     *
     * See README.md
     */
    public function getClientByPhoneNumber($data)
    {
        return $this->call('GetClientByPhoneNumber', $data);
    }

    /**
     * Get clients domains
     *
     * https://developers.whmcs.com/api-reference/getclientsdomains/
     */
    public function getClientsDomains($data = [])
    {
        return $this->call('GetClientsDomains', $data);
    }

    /**
     * Get clients products
     *
     * https://developers.whmcs.com/api-reference/getclientsproducts/
     */
    public function getClientsProducts()
    {
        return $this->call('GetClientsProducts');
    }

    /**
     * Get products
     *
     * https://developers.whmcs.com/api-reference/getproducts/
     */
    public function getProducts()
    {
        ray('getProducts');

        return $this->call('GetProducts');
    }

    /**
     * Custom: Set registrar value
     */
    public function setRegistrarSettingValue($registrar, $setting, $value)
    {
        return $this->call('SetRegistrarSettingValue', [
            'registrar' => $registrar,
            'setting' => $setting,
            'value' => $value,
        ]);
    }

    /**
     * Get registrars.
     *
     * https://developers.whmcs.com/api-reference/getregistrars/
     */
    public function getRegistrars()
    {
        return $this->call('GetRegistrars');
    }

    /**
     * Get servers
     *
     * https://developers.whmcs.com/api-reference/getservers/
     */
    public function getServers()
    {
        return $this->call('GetServers');
    }

    /**
     * Get services
     *
     * Alias for get clients products
     */
    public function getServices()
    {
        return $this->getClientsProducts();
    }

    /**
     * Module change package
     *
     * https://developers.whmcs.com/api-reference/modulechangepackage/
     */
    public function moduleChangePackage($serviceid)
    {
        return $this->call('ModuleChangePackage', [
            'serviceid' => $serviceid,
        ]);
    }

    /**
     * Set server
     *
     * Facilitates connecting to another WHMCS server
     */
    public function setServer($client)
    {
        $this->url = $client['url'];
        $this->api_identifier = $client['api_identifier'];
        $this->api_secret = $client['api_secret'];
    }

    /**
     * Update client domain.
     *
     * Requires: $domainid
     *
     * Example: $data['status' => 'Active]
     *
     * https://developers.whmcs.com/api-reference/updateclientdomain/
     */
    public function updateClientDomain($domainid, $data)
    {
        $data = array_merge([
            'domainid' => $domainid,
        ], $data);

        return $this->call('UpdateClientDomain', $data);
    }

    /**
     * Update client product
     *
     * https://developers.whmcs.com/api-reference/updateclientproduct/
     */
    public function updateClientProduct($data)
    {
        return $this->call('UpdateClientProduct', $data);
    }

    /**
     * Update service
     *
     * Alias for update client product
     */
    public function updateService($data)
    {
        return $this->updateClientProduct($data);
    }

    /**
     * Main entry point for all API calls
     */
    private function call($action, $data = [])
    {
        $postfields = [
            'identifier' => $this->api_identifier,
            'secret' => $this->api_secret,
            'action' => $action,
            'responsetype' => 'json',
        ];
        $postfields = array_merge($data, $this->mergeData, $postfields);

        // Display the input to the API call if debugging is enabled
        config('whmcs.debug') && ray($postfields);

        $apiUrl = $this->url.'/includes/api.php';

        $response = Http::withOptions(['verify' => false])
            ->asForm()
            ->post($apiUrl, $postfields);

        if (isset($response->json()['result'])) {
            if ($response->json()['result'] == 'error') {
                ray($response->json()['message'])->red();

                throw new Exception($response->json()['message']);
            }
        }

        config('whmcs.debug') && ray($response->json());

        return $response->json();
    }

    /**
     * Throw exception if the API variables have not been set
     *
     * @throws Exception
     */
    private function throwExceptionIfUrlNotPresent(): void
    {
        if (! $this->url) {
            $error = 'The WHMCS API URL was not found. Please ensure the configuration has been published and check your environment for the following three variables: WHMCS_URL, WHMCS_API_IDENTIFIER, WHMCS_API_SECRET';

            ray($error)->red();

            throw new Exception($error);
        }
    }
}
