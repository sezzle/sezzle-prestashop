<?php

namespace Sezzle\Services;

use DateTime;
use Exception;
use Sezzle\Config;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\RequestException;
use Sezzle\Model\CustomerOrder;
use Sezzle\Model\Order\Capture;
use Sezzle\Model\Session;
use Sezzle\Model\Token;
use Sezzle\Model\Tokenize;

/**
 * Class TokenizationService
 * @package Sezzle\Services
 */
class TokenizationService
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
     * Get Token Details
     *
     * @param string $token
     * @param string $tokenizeToken
     * @return Tokenize
     * @throws RequestException
     */
    public function getTokenDetails($token, $tokenizeToken)
    {
        $response = $this->clientService->sendRequest(
            Config::GET,
            sprintf(Config::TOKENIZE_RESOURCE, $tokenizeToken),
            [],
            ["Authorization" => "Bearer " . $token]
        );
        return Tokenize::fromArray($response);
    }

    /**
     * Create Order by Customer UUID
     *
     * @param string $token
     * @param string $customerUUID
     * @param array $payload
     * @return CustomerOrder
     * @throws RequestException
     */
    public function createOrder($token, $customerUUID, array $payload)
    {
        $response = $this->clientService->sendRequest(
            Config::POST,
            sprintf(Config::CUSTOMER_ORDER_RESOURCE, $customerUUID),
            $payload,
            ["Authorization" => "Bearer " . $token]
        );
        return CustomerOrder::fromArray($response);
    }
}
