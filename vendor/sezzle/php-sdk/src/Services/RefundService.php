<?php

namespace Sezzle\Services;

use Sezzle\Config;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\RequestException;
use Sezzle\Model\Order\Refund;
use Sezzle\Model\Session\Order\Amount;

/**
 * Class CaptureService
 * @package Sezzle\Services
 */
class RefundService
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
     * Refund Payment
     *
     * @param string $token
     * @param string $orderUUID
     * @param array $payload
     * @return Refund
     * @throws RequestException
     */
    public function refundPayment($token, $orderUUID, array $payload)
    {
        $response = $this->clientService->sendRequest(
            Config::POST,
            sprintf(Config::REFUND_RESOURCE, $orderUUID),
            $payload,
            ["Authorization" => "Bearer " . $token]
        );
        return Refund::fromArray($response);
    }
}
