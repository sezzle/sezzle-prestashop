<?php

namespace Sezzle\Services;

use DateTime;
use Exception;
use Sezzle\Config;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\RequestException;
use Sezzle\Model\Token;

/**
 * Class AuthenticationService
 * @package Sezzle\Services
 */
class AuthenticationService
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
     * @param array $credentials
     * @param array $platformData
     * @return Token
     * @throws RequestException
     */
    public function get(array $credentials, array $platformData)
    {
        $response = $this->clientService->sendRequest(
            Config::POST,
            Config::AUTHENTICATION_RESOURCE,
            $credentials,
            ["Sezzle-Platform" => base64_encode(json_encode($platformData))]
        );
        return Token::fromArray($response);
    }

    /**
     * @param Token $token
     * @return bool
     * @throws Exception
     */
    private function isTokenValid(Token $token)
    {
        $dateTimeNow = new DateTime();
        $dateTimeExpire = new DateTime(substr($token->getExpirationDate(), 0, 19));
        $dateTimeExpire = $dateTimeExpire->format('Y-m-d H:i:s');
        //Decrease expire date by one hour just to make sure, we don't run into an unauthorized exception.
        //$dateTimeExpire = $dateTimeExpire->sub(new \DateInterval('PT1H'));

        if ($dateTimeExpire < $dateTimeNow) {
            return false;
        }

        return true;
    }
}
