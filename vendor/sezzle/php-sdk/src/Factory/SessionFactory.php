<?php

namespace Sezzle\Factory;

use Sezzle\Model\Session;

/**
 * Class SessionFactory
 * @package Sezzle\Factory
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
