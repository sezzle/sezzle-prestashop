<?php

namespace Sezzle\Services;

use Sezzle\Config;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\RequestException;
use Sezzle\Model\Order;

/**
 * Class OrderService
 * @package Sezzle\Services
 */
class OrderService
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
     * Get Order
     *
     * @param string $token
     * @param string $orderUUID
     * @return Order
     * @throws RequestException
     */
    public function getOrder($token, $orderUUID)
    {
        $response = $this->clientService->sendRequest(
            Config::GET,
            sprintf(Config::ORDER_RESOURCE, $orderUUID),
            null,
            $token
        );
        return Order::fromArray($response);
    }

    /**
     * Update Order
     *
     * @param string $token
     * @param string $orderUUID
     * @param array $payload
     * @return bool
     * @throws RequestException
     */
    public function updateOrder($token, $orderUUID, array $payload)
    {
        $response = $this->clientService->sendRequest(
            Config::PUT,
            sprintf(Config::ORDER_RESOURCE, $orderUUID),
            $payload,
            $token
        );
        return isset($response['statusCode']) && $response['statusCode'] === strval(http_response_code(204));
    }
}
