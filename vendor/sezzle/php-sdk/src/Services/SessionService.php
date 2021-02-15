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
     * @param string $token
     * @param array $payload
     * @return Session
     * @throws RequestException
     */
    public function createSession($token, array $payload)
    {
        $response = $this->clientService->sendRequest(
            Config::POST,
            Config::SESSION_RESOURCE,
            $payload,
            $token
        );
        return Session::fromArray($response);
    }
}
