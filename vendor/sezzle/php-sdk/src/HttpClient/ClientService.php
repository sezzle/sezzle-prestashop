<?php

namespace Sezzle\HttpClient;

use RuntimeException;
use Sezzle\Config;
use Sezzle\HttpClient\GuzzleHttpClient as GuzzleClient;

class ClientService
{
    /**
     * @var array
     */
    private $headers;

    /**
     * @var string
     */
    private $gatewayUrl;

    /**
     * @var GuzzleClient
     */
    private $client;
    /**
     * @var string
     */
    private $apiMode;

    /**
     * @var string
     */
    private $gatewayRegion;

    /**
     * ClientService constructor.
     * @param GuzzleFactory $factory
     * @param string $apiMode
     * @param string $gatewayRegion
     */
    public function __construct(
        GuzzleFactory $factory,
        string $apiMode,
        string $gatewayRegion = ""
    ) {
        $this->client = new GuzzleClient($factory);
        $this->apiMode = $apiMode;
        $this->gatewayRegion = $gatewayRegion;
        $this->gatewayUrl = Config::getGatewayUrl($this->apiMode, Config::API_V2, $this->gatewayRegion);
    }

    /**
     * Sends a request and returns the response.
     * The type can be obtained from RequestType.php
     *
     * @param string $type
     * @param string $resourceUri
     * @param array $data
     * @param string $token
     * @return array
     * @throws RequestException
     */
    public function sendRequest($type, $resourceUri, array $data = [], $token = "")
    {
        if (!$this->getHeader('Authorization') && $token) {
            $this->setHeader('Authorization', 'Bearer ' . $token);
        }

        $resourceUri = $this->gatewayUrl . '/' . $resourceUri;

        if (!empty($data)) {
            $data = json_encode($data);
            $this->setHeader('Content-Type', 'application/json');
        }

        switch ($type) {
            case Config::POST:
                $response = $this->client->post($resourceUri, $this->headers, $data)->getBody();
                break;

            case Config::GET:
                $response = $this->client->get($resourceUri, $this->headers)->getBody();
                break;

            case Config::PATCH:
                $response = $this->client->patch($resourceUri, $this->headers, $data)->getBody();
                break;

            case Config::PUT:
                $response = $this->client->put($resourceUri, $this->headers, $data)->getBody();
                break;

            default:
                throw new RuntimeException('An unsupported request type was provided. The type was: ' . $type);
        }

        return json_decode($response, true);
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getHeader($key)
    {
        return $this->headers[$key];
    }
}
