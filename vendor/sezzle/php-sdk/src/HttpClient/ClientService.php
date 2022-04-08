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
    )
    {
        $this->client = new GuzzleClient($factory);
        $this->apiMode = $apiMode;
        $this->gatewayRegion = $gatewayRegion;
        $this->gatewayUrl = Config::getGatewayUrl($this->apiMode, Config::API_V2, $this->gatewayRegion);
    }

    /**
     * Sends a request and returns the response.
     * The type can be obtained from RequestType.php
     *
     * @param string $method
     * @param string $resourceUri
     * @param array $data
     * @param array $headers
     * @return array
     * @throws RequestException
     */
    public function sendRequest($method, $resourceUri, $data = [], $headers = [])
    {
        if (count($headers) > 0 ) {
            foreach ($headers as $key => $value) {
                if ($key === 'Authorization') {
                    if (!$this->getHeader('Authorization') && $value) {
                        $this->setHeader($key, $value);
                    }
                } else {
                    $this->setHeader($key, $value);
                }
            }
        }

        $resourceUri = $this->gatewayUrl . '/' . $resourceUri;

        if (!empty($data)) {
            $data = json_encode($data);
            $this->setHeader('Content-Type', 'application/json');
        }

        switch ($method) {
            case Config::POST:
                $response = $this->client->post($resourceUri, $this->headers, $data);
                break;

            case Config::GET:
                $response = $this->client->get($resourceUri, $this->headers);
                break;

            case Config::PATCH:
                $response = $this->client->patch($resourceUri, $this->headers, $data);
                break;

            case Config::PUT:
                $response = $this->client->put($resourceUri, $this->headers, $data);
                break;

            default:
                throw new RuntimeException('An unsupported request type was provided. The type was: ' . $type);
        }

        $responseBody = $response->getStatusCode() === '204' && !$response->getBody() ? '{"statusCode": "204"}' : $response->getBody();
        return json_decode($responseBody, true);
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
