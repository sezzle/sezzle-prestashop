<?php

namespace PrestaShop\Module\Sezzle\Services;

use Configuration;
use Sezzle;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\GuzzleFactory;
use Sezzle\HttpClient\RequestException;
use Sezzle\Model\AuthCredentials;
use Sezzle\Services\AuthenticationService;

/**
 * Class Authentication
 */
class Authentication
{

    /**
     * Get Authentication Token
     *
     * @return string
     * @throws RequestException
     */
    public static function getToken()
    {
        $apiMode = Configuration::get(Sezzle::$formFields["live_mode"])
            ? Sezzle::MODE_PRODUCTION
            : Sezzle::MODE_SANDBOX;
        $publicKey = Configuration::get(Sezzle::$formFields["public_key"]);
        $privateKey = Configuration::get(Sezzle::$formFields["private_key"]);

        $authModel = new AuthCredentials();
        $authModel->setPublicKey($publicKey)->setPrivateKey($privateKey);

        $tokenService = new AuthenticationService(new ClientService(
            new GuzzleFactory(),
            $apiMode
        ));
        $tokenModel = $tokenService->get($authModel->toArray());
        return $tokenModel->getToken();
    }
}
