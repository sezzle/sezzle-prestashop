<?php
/**
 * 2007-2022 PrestaShop
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

use PrestaShop\Module\Sezzle\Handler\Payment\Capture;

class AdminSezzleController extends ModuleAdminController
{

    /**
     * Capture payment
     */
    public function ajaxProcessCapturePayment()
    {
        try {
            $response = [];
            $amount = (float)Tools::getValue('amount');
            $orderReference = Tools::getValue('order_reference');
            $orders = Order::getByReference($orderReference);
            if ($orders->count() !== 1) {
                throw new PrestaShopPaymentException("Order not found.");
            }

            /** @var Order $order */
            $order = $orders[0];
            if ($order->payment !== $this->module->displayName) {
                throw new PrestaShopPaymentException("Invalid payment method.");
            }

            $txn = SezzleTransaction::getByReference($orderReference);

            // amount validation
            $amtAvailableForCapture = $txn->getAuthorizedAmount() - $txn->getReleaseAmount() - $txn->getCaptureAmount();
            $amountExceedsMaxValue = Sezzle\Util::formatToCents($amount) >
                Sezzle\Util::formatToCents($amtAvailableForCapture);
            if ($amount <= 0 || $amountExceedsMaxValue) {
                throw new PrestaShopException("Invalid amount.");
            }

            // bypass for auth and capture action
            if ($txn->getAuthorizedAmount() === $txn->getCaptureAmount() ||
                $txn->getReleaseAmount() > 0) {
                throw new PrestaShopPaymentException("Payment has been already captured.");
            }

            $captureHandler = new Capture($order);
            $captureHandler->execute($amount);
            $response['success'] = true;
        } catch (Exception $e) {
            if (isset($order) && $order instanceof Order && $order) {
                Capture::removeCaptureTransaction($order->reference);
            }
            $response['success'] = false;
            $response['msg'] = $e->getMessage();
        }

        echo json_encode($response);
        exit;
    }
}
