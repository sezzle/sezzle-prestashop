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

/**
 * Class Tokenization
 * @package PrestaShop\Module\Sezzle\Handler\Service
 */
class Tokenization
{

    /**
     * Get Tokenization Details
     *
     * @param string $tokenizeToken
     * @return Sezzle\Model\Tokenize
     * @throws Sezzle\HttpClient\RequestException
     */
    public static function getTokenizationDetails($tokenizeToken)
    {
        $apiMode = Configuration::get(Sezzle::$formFields["live_mode"])
            ? Sezzle::MODE_PRODUCTION
            : Sezzle::MODE_SANDBOX;
        $gatewayRegion = Configuration::get(Sezzle::SEZZLE_GATEWAY_REGION_KEY);

        // instantiate tokenization service
        $tokenizationService = new Sezzle\Services\TokenizationService(new ClientService(
            new GuzzleFactory(),
            $apiMode,
            $gatewayRegion
        ));

        // get tokenization response
        return $tokenizationService->getTokenDetails(
            Authentication::getToken(),
            $tokenizeToken
        );
    }

    /**
     * Create Order by Customer UUID
     *
     * @param string $customerUUID
     * @param Sezzle\Model\CustomerOrder $payload
     * @return Sezzle\Model\CustomerOrder
     * @throws Sezzle\HttpClient\RequestException
     */
    public static function createOrder($customerUUID, Sezzle\Model\CustomerOrder $payload)
    {
        $apiMode = Configuration::get(Sezzle::$formFields["live_mode"])
            ? Sezzle::MODE_PRODUCTION
            : Sezzle::MODE_SANDBOX;
        $gatewayRegion = Configuration::get(Sezzle::SEZZLE_GATEWAY_REGION_KEY);

        // instantiate tokenization service
        $tokenizationService = new Sezzle\Services\TokenizationService(new ClientService(
            new GuzzleFactory(),
            $apiMode,
            $gatewayRegion
        ));
        // get order response
        return $tokenizationService->createOrder(
            Authentication::getToken(),
            $customerUUID,
            $payload->toArray()
        );
    }
}
