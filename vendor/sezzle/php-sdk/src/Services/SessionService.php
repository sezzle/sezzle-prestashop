<?php


namespace Sezzle\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\RequestException;
use Sezzle\Model\Session;

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

    /**
     * SessionService constructor.
     * @param ClientService $clientService
     */
    public function __construct(
        ClientService $clientService
    ) {
        $this->clientService = $clientService;
    }

    /**
     * @param array $payload
     * @return Session
     * @throws RequestException
     */
    public function create(array $payload)
    {
        $response = $this->clientService->sendRequest("POST", "", $payload);
        return Session::fromArray($response);
    }
}
