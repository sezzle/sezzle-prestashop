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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
class SezzleValidationModuleFrontController extends ModuleFrontController
{
    /**
     * This class should be use by your Instant Payment
     * Notification system to validate the order remotely
     */
    public function postProcess()
    {
//        /*
//         * If the module is not active anymore, no need to process anything.
//         */
//        if ($this->module->active == false) {
//            die;
//        }
//
//        /**
//         * Since it is an example, we choose sample data,
//         * You'll have to get the correct values :)
//         */
//        $cart_id = 1;
//        $customer_id = 1;
//        $amount = 100.00;
//
//        /*
//         * Restore the context from the $cart_id & the $customer_id to process the validation properly.
//         */
//        Context::getContext()->cart = new Cart((int) $cart_id);
//        Context::getContext()->customer = new Customer((int) $customer_id);
//        Context::getContext()->currency = new Currency((int) Context::getContext()->cart->id_currency);
//        Context::getContext()->language = new Language((int) Context::getContext()->customer->id_lang);
//
//        $secure_key = Context::getContext()->customer->secure_key;
//
//        if ($this->isValidOrder() === true) {
//            $payment_status = Configuration::get('PS_OS_PAYMENT');
//            $message = null;
//        } else {
//            $payment_status = Configuration::get('PS_OS_ERROR');
//
//            /**
//             * Add a message to explain why the order has not been validated
//             */
//            $message = $this->module->l('An error occurred while processing payment');
//        }
//
//        $module_name = $this->module->displayName;
//        $currency_id = (int) Context::getContext()->currency->id;
//
//        echo $this->module->validateOrder(
//            $cart_id,
//            $payment_status,
//            $amount,
//            $module_name,
//            $message,
//            array(),
//            $currency_id,
//            false,
//            $secure_key
//        );
//        die();
//
//        return $this->module->validateOrder($cart_id, $payment_status, $amount, $module_name, $message, array(), $currency_id, false, $secure_key);

        if (!($this->module instanceof Sezzle)) {
            Tools::redirect('index.php?controller=order&step=1');

            return;
        }

        $cart = $this->context->cart;

        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');

            return;
        }

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'sezzle') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            die($this->l('This payment method is not available.'));
        }

        $customer = new Customer($cart->id_customer);

        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');

            return;
        }

        $currency = $this->context->currency;
        $total = (float) $cart->getOrderTotal(true, Cart::BOTH);

//        $mailVars = [
//            '{check_name}' => Configuration::get('CHEQUE_NAME'),
//            '{check_address}' => Configuration::get('CHEQUE_ADDRESS'),
//            '{check_address_html}' => str_replace("\n", '<br />', Configuration::get('CHEQUE_ADDRESS')), ];

        $this->module->validateOrder(
            (int) $cart->id,
            (int) Configuration::get('PS_OS_PAYMENT'),
            $total,
            $this->module->displayName,
            null,
            null,
            (int) $currency->id,
            false,
            $customer->secure_key
        );
        Tools::redirect('index.php?controller=order-confirmation&id_cart=' . (int) $cart->id . '&id_module=' . (int) $this->module->id . '&id_order=' . $this->module->currentOrder . '&key=' . $customer->secure_key);
    }

    protected function isValidOrder()
    {
        /*
         * Add your checks right there
         */
        return true;
    }
}
