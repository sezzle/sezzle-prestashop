<?php

namespace Sezzle\HttpClient;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;

class GuzzleHttpClient implements HttpClientInterface
{
    /**
     * @var ClientInterface
     */
    private $guzzleClient;

    public function __construct(GuzzleFactory $guzzleFactory, array $guzzleConfig = [])
    {
        $this->guzzleClient = $guzzleFactory->createClient($guzzleConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function get($url = null, $headers = [])
    {
        try {
            $response = $this->guzzleClient->get($url, ['headers' => $headers]);
        } catch (\Exception $e) {
            /** @var GuzzleClientException $e */
            $body = '';
            if ($e->hasResponse()) {
                $body = (string) $e->getResponse()->getBody();
            }

            throw new RequestException(
                $e->getMessage(),
                $e->getCode(),
                $e,
                $body
            );
        }

        return new Response(
            (string) $response->getStatusCode(),
            $response->getHeaders(),
            (string) $response->getBody()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function head($url = null, array $headers = [])
    {
        try {
            $response = $this->guzzleClient->head($url, ['headers' => $headers]);
        } catch (\Exception $e) {
            throw new RequestException($e->getMessage(), $e->getCode(), $e);
        }

        return new Response(
            (string) $response->getStatusCode(),
            $response->getHeaders(),
            (string) $response->getBody()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete($url = null, array $headers = [])
    {
        try {
            $response = $this->guzzleClient->delete($url, ['headers' => $headers]);
        } catch (\Exception $e) {
            throw new RequestException($e->getMessage(), $e->getCode(), $e);
        }

        return new Response(
            (string) $response->getStatusCode(),
            $response->getHeaders(),
            (string) $response->getBody()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function put($url = null, array $headers = [], $content = null)
    {
        try {
            // http://guzzle.readthedocs.org/en/latest/clients.html#request-options
            $options = [
                'headers' => $headers,
                'body' => $content,
            ];

            $response = $this->guzzleClient->put($url, $options);
        } catch (\Exception $e) {
            throw new RequestException($e->getMessage(), $e->getCode(), $e);
        }

        return new Response(
            (string) $response->getStatusCode(),
            $response->getHeaders(),
            (string) $response->getBody()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function patch($url = null, array $headers = [], $content = null)
    {
        try {
            // http://guzzle.readthedocs.org/en/latest/clients.html#request-options
            $options = [
                'headers' => $headers,
                'body' => $content,
            ];

            $response = $this->guzzleClient->patch($url, $options);
        } catch (\Exception $e) {
            throw new RequestException($e->getMessage(), $e->getCode(), $e);
        }

        return new Response(
            (string) $response->getStatusCode(),
            $response->getHeaders(),
            (string) $response->getBody()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function post($url = null, array $headers = [], $content = null)
    {
        try {
            // http://guzzle.readthedocs.org/en/latest/clients.html#request-options
            $options = [
                'headers' => $headers,
                'body' => $content,
            ];

            $response = $this->guzzleClient->post($url, $options);
        } catch (\Exception $e) {
            /** @var GuzzleClientException $e */
            $body = '';
            if ($e->hasResponse()) {
                $body = (string) $e->getResponse()->getBody();
            }

            throw new RequestException(
                $e->getMessage(),
                $e->getCode(),
                $e,
                $body
            );
        }

        return new Response(
            (string) $response->getStatusCode(),
            $response->getHeaders(),
            (string) $response->getBody()
        );
    }
}
