<?php

namespace Sezzle\HttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class GuzzleFactory
{
    /**
     * @return ClientInterface
     */
    public function createClient(array $guzzleConfig = [])
    {
        return new Client($guzzleConfig);
    }
}
