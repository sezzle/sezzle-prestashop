<?php
/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Sezzle <dev@sezzle.com>
 * @copyright Copyright (c) Sezzle
 * @license   https://www.apache.org/licenses/LICENSE-2.0.txt  Apache 2.0 License
 */

namespace PrestaShop\Module\Sezzle\Handler;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Configuration;
use PrestaShop\Module\Sezzle\Handler\Service\Authentication;
use Sezzle;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\GuzzleFactory;
use Sezzle\Model\AuthCredentials;
use Sezzle\Services\AuthenticationService;
use Tools;

class GatewayRegion
{

    /**
     * Get Gateway Region
     *
     * @return string
     */
    public function get()
    {
        $storedGatewayRegion = Configuration::get(Sezzle::SEZZLE_GATEWAY_REGION_KEY);
        if (!$this->hasKeysConfigurationChanged() && $storedGatewayRegion) {
            return $storedGatewayRegion;
        }

        foreach (Sezzle::SUPPORTED_REGIONS as $region) {
            try {
                if (Authentication::authenticate($region, false)->getToken()) {
                    return $region;
                }
            } catch (Sezzle\HttpClient\RequestException $e) {
                continue;
            }
        }
        return "";
    }

    /**
     * Checking API Keys Configuration has changed or not
     *
     * @return bool
     */
    private function hasKeysConfigurationChanged()
    {
        // stored data
        $storedApiMode = Configuration::get(Sezzle::$formFields["live_mode"])
            ? Sezzle::MODE_PRODUCTION
            : Sezzle::MODE_SANDBOX;
        $storedPublicKey = Configuration::get(Sezzle::$formFields["public_key"]);
        $storedPrivateKey = Configuration::get(Sezzle::$formFields["private_key"]);

        // input data
        $apiMode = Tools::getValue(Sezzle::$formFields["live_mode"])
            ? Sezzle::MODE_PRODUCTION
            : Sezzle::MODE_SANDBOX;
        $publicKey = Tools::getValue(Sezzle::$formFields["public_key"]);
        $privateKey = Tools::getValue(Sezzle::$formFields["private_key"]);

        // return stored region if the key elements match
        if ($storedPublicKey === $publicKey
            && $storedPrivateKey === $privateKey
            && $storedApiMode === $apiMode) {
            return false;
        }
        return true;
    }
}
