<?php


namespace PrestaShop\Module\Sezzle\Factory;

use Prestashop\Module\Sezzle\Model\AuthCredentials;

/**
 * Class AuthFactory
 * @package PrestaShop\Module\Sezzle\Factory
 */
class AuthFactory
{
    /**
     * @param mixed ...$params
     * @return AuthCredentials
     */
    public static function create(...$params)
    {
        $authModel = new AuthCredentials();
        $authModel->setPublicKey($params[0]);
        $authModel->setPrivateKey($params[1]);
        return $authModel;
    }
}
