<?php

use PrestaShop\Module\Sezzle\Handler\Service\Session;

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
class SezzleRedirectModuleFrontController extends SezzleAbstractModuleFrontController
{
    /**
     * Checkout building before redirection to Sezzle Checkout
     * @throws Exception
     */
    public function postProcess()
    {
        $cart = $this->context->cart;
        // If the below condition does not satisfy, no need to process anything.
        if (!$this->isCheckoutValid()) {
            $this->handleError('Checkout Validation failed.');
        }

        // Check that this payment option is still available in case
        // the customer changed his address just before the end of the checkout process
        $isAvailable = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] === $this->module->name) {
                $isAvailable = true;
                break;
            }
        }

        if (!$isAvailable) {
            $this->handleError('This payment method is not available.
                           Please contact website administrator.');
        }

        // use uncompleted checkout session if any
        $txn = SezzleTransaction::getByCartId($this->context->cart->id);
        if (!$txn->getReference() && $txn->getAuthorizedAmount() == 0 && $checkoutUrl = $txn->getCheckoutUrl()) {
            Tools::redirectLink($checkoutUrl);
        }

        // session build and redirect
        try {
            $session = new Session($cart);
            $checkoutSession = $session->createSession();
            if (!$checkoutSession->getOrder()) {
                throw new Exception("Error creating session");
            }
            $txn = new SezzleTransaction();
            $txn->storeCheckoutSession($cart, $checkoutSession);
            Tools::redirectLink($checkoutSession->getOrder()->getCheckoutUrl());
        } catch (Exception $e) {
            PrestaShopLogger::addLog($e->getMessage(), 3, null, "Sezzle", 1);
            $this->handleError('Error while creating checkout. Please try again.');
        }
    }
}
