<?php

use PrestaShop\Module\Sezzle\ServiceHandler\Capture;
use PrestaShop\Module\Sezzle\ServiceHandler\Order as SezzleOrder;
use Sezzle\HttpClient\RequestException;

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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
class SezzleCompleteModuleFrontController extends SezzleAbstarctModuleFrontController
{
    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws Exception
     */
    public function postProcess()
    {
        $cart = $this->context->cart;
        $txn = SezzleTransaction::getByCartId($this->context->cart->id);

        // validate checkout
        if (!$this->isCheckoutValid($txn)) {
            $this->handleError('Unable to validate the Checkout.');
        }

        $orderUuid = $txn->getOrderUuid();
        $customer = new Customer((int)$cart->id_customer);

        // order create in store
        $isOrderValid = $this->module->validateOrder(
            $cart->id,
            Configuration::get('SEZZLE_AWAITING_PAYMENT'),
            $cart->getOrderTotal(),
            $this->module->name,
            "",
            [],
            (int)Context::getContext()->currency->id,
            false,
            $customer->secure_key
        );

        if (!$isOrderValid) {
            $this->handleError("Failed to create the order.");
        }

        $order = Order::getByCartId((int)$cart->id);

        // capture handling
        $paymentAction = Configuration::get(Sezzle::$formFields['payment_action']);
        if ($paymentAction === Sezzle::ACTION_AUTHORIZE_CAPTURE) {
            try {
                $captureService = new Capture($cart);
                $response = $captureService->capturePayment($orderUuid, false);
                if ($response->getUuid()) {
                    SezzleTransaction::storeCaptureAmount($cart->getOrderTotal(), $orderUuid);
                    $order->setCurrentState(Configuration::get('PS_OS_PAYMENT'));
                    $order->save();
                }
            } catch (RequestException $e) {
                $this->handleError("Failed to process the payment");
            } catch (PrestaShopException $e) {
                // ignore this exception as of now
            }
        }

        if (!$order->id) {
            // An error occured and is shown on a new page.
            $this->handleError(
                'An error occured while fetching order details.
             Please contact the merchant to have more informations'
            );
        }

        // order reference handle in store and sezzle
        SezzleTransaction::storeOrderReference($order->reference, $txn->getOrderUuid());
        try {
            SezzleOrder::updateOrderReferenceId($orderUuid, $order->reference);
        } catch (RequestException $e) {
            // ignore this exception as of now
        }
        // The order has been placed so we redirect the customer on the confirmation page.
        Tools::redirect(
            'index.php?controller=order-confirmation&id_cart=' . $cart->id .
            '&id_module=' . $this->module->id . '&id_order=' . $order->id . '&key=' . $customer->secure_key
        );
    }
}
