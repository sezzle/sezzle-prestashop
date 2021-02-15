<?php

namespace PrestaShop\Module\Sezzle\Factory;

use Prestashop\Module\Sezzle\Model\Session;

/**
 * Class SessionFactory
 * @package PrestaShop\Module\Sezzle\Factory
 */
class SessionFactory
{
    /**
     * @param array $params
     * @return Session
     */
    public static function create(array $params)
    {
        return new Session();
    }
}
