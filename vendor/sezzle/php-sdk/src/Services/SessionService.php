<?php

namespace Sezzle\Services;

use DateTime;
use Exception;
use Sezzle\Config;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\RequestException;
use Sezzle\Model\Session;
use Sezzle\Model\Token;

/**
 * Class SessionService
 * @package Sezzle\Services
 */
class SessionService
{
    /**
     * @var ClientService
     */
    private $clientService;

    public function __construct(
        ClientService $clientService
    ) {
        $this->clientService = $clientService;
    }

    /**
     * Create Session
     *
     * @param string $token
     * @param array $payload
     * @return Session
     * @throws RequestException
     */
    public function createSession($token, array $payload)
    {
        $response = $this->clientService->sendRequest(
            Config::POST,
            sprintf(Config::SESSION_RESOURCE, ""),
            $payload,
            ["Authorization" => "Bearer " . $token]
        );
        return Session::fromArray($response);
    }

    /**
     * Get Session
     *
     * @param string $token
     * @param string $orderUUID
     * @return Session
     * @throws RequestException
     */
    public function getSession($token, $orderUUID)
    {
        $response = $this->clientService->sendRequest(
            Config::POST,
            sprintf(Config::SESSION_RESOURCE, $orderUUID),
            [],
            ["Authorization" => "Bearer " . $token]
        );
        return Session::fromArray($response);
    }
}
