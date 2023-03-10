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

if (!defined('_PS_VERSION_')) {
    exit;
}

use Currency;
use ObjectModel;
use OrderSlip;
use OrderCore as CoreOrder;
use Payment;
use PrestaShop\Module\Sezzle\Handler\Order;
use PrestaShop\Module\Sezzle\Handler\Service\Refund as RefundServiceHandler;
use PrestaShop\Module\Sezzle\Handler\Service\Util;
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
     * @param bool $isPartial
     * @throws PrestaShopException
     * @throws RequestException
     */
    public function execute($isPartial = false)
    {
        $payments = $this->order->getOrderPayments();
        if (count($payments) === 0) {
            throw new PrestaShopException("Order Payments not found.");
        }
        $txn = SezzleTransaction::getByReference($this->order->reference);

        // full refund
        if (!$isPartial) {
            if ($txn->getReleaseAmount() != 0 || $txn->getCaptureAmount() !== $txn->getAuthorizedAmount()) {
                throw new PrestaShopException("Invalid refund request.");
            }
            $amount = $this->order->total_paid;
        } else {
            // bypass if payment is not still captured or in case full refund is done
            if ($txn->getCaptureAmount() == 0 || $txn->getRefundAmount() === $txn->getCaptureAmount()) {
                throw new PrestaShopException("Invalid refund request.");
            }
            /** @var OrderSlip $orderSlip */
            $orderSlip = $this->getLastOrderSlip();
            $amount = $orderSlip->amount + $orderSlip->shipping_cost_amount;
        }

        $refundPayload = $this->buildRefundPayload($amount);

        // refund action
        $response = RefundServiceHandler::refundPayment(
            $txn->getOrderUUID(),
            $refundPayload
        );
        if ($response->getUuid()) {
            // post refund handling
            $finalAmount = $txn->getRefundAmount() + $amount;
            SezzleTransaction::storeRefundAmount($finalAmount, $txn->getOrderUUID());
        }
    }

    /**
     * Build Refund Payload
     *
     * @param float $amount
     * @return Sezzle\Model\Session\Order\Amount
     */
    public function buildRefundPayload($amount)
    {
        $currency = new Currency($this->order->id_currency);
        return Util::getAmountObject(
            Sezzle\Util::formatToCents($amount),
            $currency->iso_code
        );
    }

    /**
     * Remove Refund Transaction
     *
     * @param int $orderId
     */
    public static function removeRefundTransaction($orderId)
    {
        Payment::deleteRefund($orderId);
    }

    /**
     * Get last order slip
     *
     * @return bool|ObjectModel|null
     */
    private function getLastOrderSlip()
    {
        $orderSlipsCollection = $this->order->getOrderSlipsCollection();
        if (method_exists($orderSlipsCollection, 'getLast')) {
            return $orderSlipsCollection->getLast();
        }

        $orderSlips = $orderSlipsCollection->getAll();
        if (!count($orderSlips)) {
            return false;
        }

        return $orderSlips[count($orderSlips) - 1];
    }
}
