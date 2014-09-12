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

    public function __construct(array $config) {

        if (!isset($config[Options::KEY])) {
            throw new \InvalidArgumentException("No access key defined");
        }

        if (!isset($config[Options::SECRET])) {
            throw new \InvalidArgumentException("No secret key defined");
        }

        $this->config = (object) $config;
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
     * @return \Guzzle\Http\Message\RequestInterface
     */
    public function authenticate($username, $password)
    {
        $grant_type = "password";

        $client = new Client();

        $request = $client->post('https://dev-api.goreact.com/oauth/token', array(), array(
            'username' => $username,
            'password' => $password,
            'client_id' => $this->config->key,
            'client_secret' => $this->config->secret,
            'grant_type' => $grant_type
        ));

        return $request->send();
    }
}