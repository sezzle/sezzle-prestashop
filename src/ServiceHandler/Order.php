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

namespace PrestaShop\Module\Sezzle\ServiceHandler;

use Cart;
use Configuration;
use Currency;
use Sezzle;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\GuzzleFactory;

/**
 * Class Order
 * @package PrestaShop\Module\Sezzle\ServiceHandler
 */
class Order
{

    /**
     * Update Order Reference ID
     *
     * @param string $orderUuid
     * @param string $referenceId
     * @return bool
     * @throws Sezzle\HttpClient\RequestException
     */
    public static function updateOrderReferenceId($orderUuid, $referenceId)
    {
        $apiMode = Configuration::get(Sezzle::$formFields["live_mode"])
            ? Sezzle::MODE_PRODUCTION
            : Sezzle::MODE_SANDBOX;
        $orderService = new Sezzle\Services\OrderService(new ClientService(
            new GuzzleFactory(),
            $apiMode
        ));
        return $orderService->updateOrder(
            Authentication::getToken(),
            $orderUuid,
            ['reference_id' => $referenceId]
        );
    }
}
