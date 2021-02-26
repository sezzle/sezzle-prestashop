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

use Configuration;
use Currency;
use OrderCore;
use Sezzle;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\GuzzleFactory;

/**
 * Class Refund
 * @package PrestaShop\Module\Sezzle\Handler\Service
 */
class Refund
{

    /**
     * Refund Payment
     *
     * @param string $orderUUID
     * @param float $amount
     * @param string $currencyCode
     * @return Sezzle\Model\Order\Refund
     * @throws Sezzle\HttpClient\RequestException
     */
    public static function refundPayment($orderUUID, $amount, $currencyCode)
    {
        // refund payload building
        $refundModel = Util::getAmountObject(
            Sezzle\Util::formatToCents($amount),
            $currencyCode
        );

        $apiMode = Configuration::get(Sezzle::$formFields["live_mode"])
            ? Sezzle::MODE_PRODUCTION
            : Sezzle::MODE_SANDBOX;

        // instantiate refund service
        $refundService = new Sezzle\Services\RefundService(new ClientService(
            new GuzzleFactory(),
            $apiMode
        ));

        // get refund response
        return $refundService->refundPayment(
            Authentication::getToken(),
            $orderUUID,
            $refundModel->toArray()
        );
    }
}
