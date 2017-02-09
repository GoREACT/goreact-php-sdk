<?php

use GoReact\GoClient;

require_once 'vendor/autoload.php';

$options = [
    "access_key" => "",
    "secret_key" => "",
    "environment" => ""
];

$username = '';
$password = '';
$provider = '';

$client = new GoClient($options);
$response = $client->authenticate($username, $password, $provider);

print_r(json_decode($response->getBody()));exit;