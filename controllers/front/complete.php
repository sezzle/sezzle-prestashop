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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
class SezzleCompleteModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $cart = $this->context->cart;
        $customer = new Customer((int) $cart->id_customer);
        $isValid = $this->module->validateOrder(
            $cart->id,
            Configuration::get('PS_OS_PAYMENT'),
            $cart->getOrderTotal(),
            $this->module->name,
            "",
            array(),
            (int) Context::getContext()->currency->id,
            false,
            $customer->secure_key
        );

        if (!$isValid) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $orderId = Order::getIdByCartId((int) $cart->id);
        if ($orderId) {
            /**
             * The order has been placed so we redirect the customer on the confirmation page.
             */
            Tools::redirect(
                'index.php?controller=order-confirmation&id_cart=' . $cart->id .
                '&id_module=' . $this->module->id . '&id_order=' . $orderId . '&key=' . $customer->secure_key
            );
        } else {
            /*
             * An error occured and is shown on a new page.
             */
            $this->errors[] = $this->module->l(
                'An error occured. Please contact the merchant to have more informations'
            );

            $this->redirectWithNotifications('index.php?controller=order&step=1');
        }
    }
}
