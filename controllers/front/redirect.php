<?php

use PrestaShop\Module\Sezzle\Services\Session;

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
class SezzleRedirectModuleFrontController extends ModuleFrontController
{
    /**
     * Do whatever you have to before redirecting the customer on the website of your payment processor.
     */
    public function postProcess()
    {
        $cart = $this->context->cart;
        /*
         * If the below condition does not satisfy, no need to process anything.
         */
        if ($cart->id_customer == 0
            || $cart->id_address_delivery == 0
            || $cart->id_address_invoice == 0
            || !$this->module->active
            || empty($this->context->cart->getProducts())) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        // Check that this payment option is still available in case
        // the customer changed his address just before the end of the checkout process
        $isAvailable = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] === 'sezzle') {
                $isAvailable = true;
                break;
            }
        }

        if (!$isAvailable) {
            $this->checkoutErrorRedirect(
                $this->module->l(
                    'This payment method is not available.
                           Please contact website administrator.',
                    'validation'
                )
            );
        }

        try {
            $session = new Session($cart);
            $checkoutUrl = $session->createSession()->getOrder()->getCheckoutUrl();
            Tools::redirectLink($checkoutUrl);
        } catch (Exception $e) {
            PrestaShopLogger::addLog($e->getMessage(), 3, null, "Sezzle", 1);
            $this->checkoutErrorRedirect(
                $this->module->l(
                    'Error while creating checkout. Please try again.',
                    'validation'
                )
            );
        }
    }

    /**
     * Error Redirection Handler
     *
     * @param string $message
     */
    private function checkoutErrorRedirect($message)
    {
        if (!empty($message)) {
            $this->errors[] = $this->l($message);
        }
        $this->redirectWithNotifications('index.php?controller=order&step=1');
    }
}
