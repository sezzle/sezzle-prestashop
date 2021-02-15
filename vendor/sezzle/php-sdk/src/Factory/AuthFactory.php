<?php


namespace Sezzle\Factory;

use Sezzle\Model\AuthCredentials;

/**
 * Class AuthFactory
 * @package Sezzle\Factory
 */
class AuthFactory
{
    /**
     * @param mixed ...$params
     * @return AuthCredentials
     */
    public static function create(...$params)
    {
        $base = array_shift($params);
        $authModel = new AuthCredentials();
        $authModel->setPublicKey($base[0]);
        $authModel->setPrivateKey($base[1]);
        return $authModel;
    }
}
