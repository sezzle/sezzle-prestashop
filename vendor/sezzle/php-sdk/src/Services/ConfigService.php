<?php

namespace Sezzle\Services;

use Sezzle\Config;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\RequestException;

/**
 * Class ConfigService
 * @package Sezzle\Services
 */
class ConfigService
{
    /**
     * @var ClientService
     */
    private $clientService;

    public function __construct(
        ClientService $clientService
    )
    {
        $this->clientService = $clientService;
    }

    /**
     * Send config
     *
     * @param string $token
     * @return bool
     * @param array $payload
     * @throws RequestException
     */
    public function sendConfig($token, array $payload)
    {
        $response = $this->clientService->sendRequest(
            Config::POST,
            Config::CONFIG_RESOURCE,
            $payload,
            ["Authorization" => "Bearer " . $token]
        );
        return isset($response['statusCode']) && $response['statusCode'] === "204";
    }
}
