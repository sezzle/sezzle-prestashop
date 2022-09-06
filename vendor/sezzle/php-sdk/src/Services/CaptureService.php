<?php

namespace Sezzle\Services;

use DateTime;
use Exception;
use Sezzle\Config;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\RequestException;
use Sezzle\Model\Order\Capture;
use Sezzle\Model\Session;
use Sezzle\Model\Token;

/**
 * Class CaptureService
 * @package Sezzle\Services
 */
class CaptureService
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
     * Capture Payment
     *
     * @param string $token
     * @param string $orderUUID
     * @param array $payload
     * @return Capture
     * @throws RequestException
     */
    public function capturePayment($token, $orderUUID, array $payload)
    {
        $response = $this->clientService->sendRequest(
            Config::POST,
            sprintf(Config::CAPTURE_RESOURCE, $orderUUID),
            $payload,
            ["Authorization" => "Bearer " . $token]
        );
        return Capture::fromArray($response);
    }
}
