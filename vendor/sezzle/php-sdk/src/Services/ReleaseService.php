<?php

namespace Sezzle\Services;

use Sezzle\Config;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\RequestException;
use Sezzle\Model\Order\Release;
use Sezzle\Model\Session\Order\Amount;

/**
 * Class ReleaseService
 * @package Sezzle\Services
 */
class ReleaseService
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
     * Release Payment
     *
     * @param string $token
     * @param string $orderUUID
     * @param array $payload
     * @return Release
     * @throws RequestException
     */
    public function releasePayment($token, $orderUUID, array $payload)
    {
        $response = $this->clientService->sendRequest(
            Config::POST,
            sprintf(Config::RELEASE_RESOURCE, $orderUUID),
            $payload,
            $token
        );
        return Release::fromArray($response);
    }
}
