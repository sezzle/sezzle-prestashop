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

namespace PrestaShop\Module\Sezzle\Handler\Payment;

use Currency;
use Payment;
use OrderCore as CoreOrder;
use PrestaShop\Module\Sezzle\Handler\Order;
use PrestaShop\Module\Sezzle\Handler\Service\Refund as RefundServiceHandler;
use PrestaShopException;
use Sezzle;
use Sezzle\HttpClient\RequestException;
use SezzleTransaction;

/**
 * Class Refund
 * @package PrestaShop\Module\Sezzle\Handler\Payment
 */
class Refund extends Order
{
    /**
     * @var CoreOrder
     */
    private $order;

    /**
     * Capture constructor.
     * @param CoreOrder $order
     */
    public function __construct(CoreOrder $order)
    {
        $this->order = $order;
    }

    /**
     * Refund Action
     *
     * @param string $orderUUID
     * @throws PrestaShopException
     * @throws RequestException
     */
    public function execute($orderUUID)
    {
        if (!$orderUUID) {
            throw new PrestaShopException("Order UUID not found.");
        }
        $payments = $this->order->getOrderPayments();
        if (count($payments) === 0) {
            return;
        }
        $payment = $payments[0]->payment_method;
        if ($payment !== Sezzle::DISPLAY_NAME) {
            return;
        }
        $currency = new Currency($this->order->id_currency);
        $amount = $this->order->getTotalPaid();
        $response = RefundServiceHandler::refundPayment(
            $orderUUID,
            $amount,
            $currency->iso_code
        );
        if ($refundUuid = $response->getUuid()) {
            SezzleTransaction::storeRefundAmount($amount, $orderUUID);
        }
    }
}
