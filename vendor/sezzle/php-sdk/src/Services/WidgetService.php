<?php

namespace Sezzle\Services;

use Sezzle\Config;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\RequestException;

/**
 * Class WidgetService
 * @package Sezzle\Services
 */
class WidgetService
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
     * Add to Widget Queue
     *
     * @param string $token
     * @return bool
     * @throws RequestException
     */
    public function addToWidgetQueue($token)
    {
        $response = $this->clientService->sendRequest(
            Config::POST,
            Config::WIDGET_QUEUE_RESOURCE,
            [],
            $token
        );
        return isset($response['statusCode']) && $response['statusCode'] === "204";
    }
}
