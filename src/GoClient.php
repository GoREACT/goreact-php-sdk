<?php

namespace GoReact;

use Guzzle\Http\Client;

/**
 * GoReact Client
 *
 * @author  Dallin Scherbel <dallin@speakworks.com>
 */
class GoClient
{
    private $config;

    private $client;

    const BASE_PROVIDER = "goreact";

    const ENVIRONMENT_DEV = "development";
    const ENVIRONMENT_PROD = "production";
    const ENDPOINT_DEV = "https://dev-api.goreact.com";
    const ENDPOINT_PROD = "https://api.goreact.com";
    const CA_PATH_PROD = "/etc/pki/tls/certs/";

    public function __construct(array $config) {

        $this->config = (object) $config;

        if (!isset($this->config->{Options::KEY})) {
            throw new \InvalidArgumentException("No access key defined (client id)");
        }

        if (!isset($this->config->{Options::SECRET})) {
            throw new \InvalidArgumentException("No secret key defined");
        }

        // default to dev environment
        if (!isset($this->config->{Options::ENVIRONMENT})) {
            $this->config->{Options::ENVIRONMENT} = GoClient::ENVIRONMENT_DEV;
        }

        // if environment is not supported
        if(!in_array($this->config->{Options::ENVIRONMENT}, array(GoClient::ENVIRONMENT_DEV, GoClient::ENVIRONMENT_PROD))) {
            throw new \InvalidArgumentException("Environment {$this->config->{Options::ENVIRONMENT}} Not Supported");
        }

        // instantiate guzzle client
        $this->client = new Client();

        if($this->config->{Options::ENVIRONMENT} === GoClient::ENVIRONMENT_PROD) {
            $this->config->base_url = GoClient::ENDPOINT_PROD;

            // curl opts - set peer certificate issuer for production
            $this->client->getConfig()->set('curl.options', array(CURLOPT_CAPATH => self::CA_PATH_PROD));
        } else {
            $this->config->base_url = GoClient::ENDPOINT_DEV;
        }
    }

    /**
     * Authenticate with GoReact credentials
     *
     * Description: This grants an oauth bearer token for the application.
     * This token will be contextually scoped to this user and will validate permissions
     * for resource access for this client on each request. [Resource owner password Grant Type flow.]
     *
     * Response: JSON object containing (Bearer Token, Refresh Token)
     *
     * @param $username
     * @param $password
     * @param $provider_name
     * @return \Guzzle\Http\Message\RequestInterface
     */
    public function authenticate($username, $password, $provider_name = self::BASE_PROVIDER)
    {
        $grant_type = "password";

        $request = $this->client->post($this->config->base_url . Methods::GET_TOKEN, array(), array(
            'username' => $username,
            'password' => $password,
            'provider' => $provider_name,
            'client_id' => $this->config->key,
            'client_secret' => $this->config->secret,
            'grant_type' => $grant_type
        ));

        return $request->send();
    }
}