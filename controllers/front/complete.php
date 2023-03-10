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

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\Module\Sezzle\Handler\Payment\Authorization;
use PrestaShop\Module\Sezzle\Handler\Payment\Capture;
use PrestaShop\Module\Sezzle\Handler\Service\Order as SezzleOrder;
use PrestaShop\Module\Sezzle\Handler\Tokenization;
use Sezzle\HttpClient\RequestException;

class SezzleCompleteModuleFrontController extends SezzleAbstractModuleFrontController
{
    /**
     * Order process after successful Sezzle Checkout
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws Exception
     */
    public function postProcess()
    {
        $cart = $this->context->cart;
        $txn = SezzleTransaction::getByCartId($this->context->cart->id);

        // validate checkout
        if (!$this->isCheckoutValid($txn, true)) {
            $this->handleError('Unable to validate the Checkout.');
        }

        $customer = new Customer((int)$cart->id_customer);

        // handle tokenization storing
        if (Tools::getValue('customer-uuid') && $token = $this->context->cookie->token) {
            Tokenization::storeTokenizationRecords($customer->id, $token);
            unset($this->context->cookie->token);
        }

        // order create in store
        $isOrderValid = $this->module->validateOrder(
            $cart->id,
            Configuration::get('SEZZLE_AWAITING_PAYMENT'),
            $cart->getOrderTotal(),
            $this->module->displayName,
            "",
            [],
            (int)Context::getContext()->currency->id,
            false,
            $customer->secure_key
        );

        if (!$isOrderValid) {
            $this->handleError("Failed to create the order.");
        }
        $orderUUID = $txn->getOrderUUID();

        // authorization handling
        if ($this->sezzleOrder && $this->sezzleOrder->getAuthorization()) {
            $authorizeHandler = new Authorization();
            $authorizeHandler->execute($orderUUID, $this->sezzleOrder->getAuthorization());
        }

        if (method_exists(Order::class, 'getByCartId')) {
            $order = Order::getByCartId((int)$cart->id);
        } else {
            $orderId = (int)Order::getOrderByCartId((int)$cart->id);
            $order = ($orderId > 0) ? new Order($orderId) : null;
        }

        // capture handling
        $paymentAction = Configuration::get(Sezzle::$formFields['payment_action']);
        if ($paymentAction === Sezzle::ACTION_AUTHORIZE_CAPTURE) {
            try {
                $captureHandler = new Capture($order);
                $captureHandler->execute($order->total_paid);
            } catch (RequestException $e) {
                $this->handleError($e->getMessage());
            }
        }

        if (!$order->id) {
            // An error occured and is shown on a new page.
            $this->handleError(
                'An error occured while fetching order details.
             Please contact the merchant to have more informations'
            );
        }

        // order reference handling in store and sezzle
        SezzleTransaction::storeOrderReference($order->reference, $txn->getOrderUUID());
        try {
            SezzleOrder::updateOrderReferenceId($orderUUID, $order->reference);
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
