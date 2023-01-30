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

namespace PrestaShop\Module\Sezzle\Handler\Service;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Configuration;
use Sezzle;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\GuzzleFactory;
use Sezzle\HttpClient\RequestException;
use Sezzle\Model\AuthCredentials;
use Sezzle\Services\AuthenticationService;
use PrestaShop\Module\Sezzle\Handler\Util;
use Tools;

/**
 * Class Authentication
 * @package PrestaShop\Module\Sezzle\Handler\Service
 */
class Authentication
{

    /**
     * Authenticate keys
     *
     * @return Sezzle\Model\Token
     * @throws RequestException
     */
    public static function authenticate($gwRegion = "", $storedData = true)
    {
        $liveMode = $storedData ? Configuration::get(Sezzle::$formFields["live_mode"]) :
            Tools::getValue(Sezzle::$formFields['live_mode']);
        $apiMode = $liveMode ? Sezzle::MODE_PRODUCTION : Sezzle::MODE_SANDBOX;
        $publicKey = $storedData ? Configuration::get(Sezzle::$formFields["public_key"]) :
            Tools::getValue(Sezzle::$formFields['public_key']);
        $privateKey = $storedData ? Configuration::get(Sezzle::$formFields["private_key"]) :
            Tools::getValue(Sezzle::$formFields['private_key']);
        $gatewayRegion = $gwRegion ?: Configuration::get(Sezzle::SEZZLE_GATEWAY_REGION_KEY);

        // auth credentials set
        $authModel = new AuthCredentials();
        $authModel->setPublicKey($publicKey)->setPrivateKey($privateKey);

        // instantiate authentication service
        $tokenService = new AuthenticationService(new ClientService(
            new GuzzleFactory(),
            $apiMode,
            $gatewayRegion
        ));

        // get token model
        return $tokenService->get($authModel->toArray(), Util::getPlatformData());
    }

}
