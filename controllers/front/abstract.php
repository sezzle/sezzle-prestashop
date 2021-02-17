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

/**
 * Class SezzleAbstarctModuleFrontController
 */
abstract class SezzleAbstarctModuleFrontController extends ModuleFrontController
{

    /**
     * Validate Checkout
     *
     * @param SezzleTransaction $sezzleTransaction
     * @return bool
     * @throws Exception
     */
    public function isCheckoutValid(SezzleTransaction $sezzleTransaction)
    {
        $cart = $this->context->cart;
        if (!$sezzleTransaction->getIdSezzleTransaction()) {
            return false;
        }
        if (!$this->isAmountMatched($sezzleTransaction->getAuthorizedAmount(), $cart->getOrderTotal())) {
            return false;
        }
        return true;
    }

    /**
     * Match Amount
     *
     * @param float $prevAmount
     * @param float $newAmount
     * @return bool
     */
    public function isAmountMatched($prevAmount, $newAmount)
    {
        $authorizedAmount = Tools::ps_round($prevAmount, Context::getContext()->getComputingPrecision());
        if ($authorizedAmount !== $newAmount) {
            return false;
        }
        return true;
    }

    /**
     * Handle Error
     *
     * @param string $msg
     */
    public function handleError($msg = "")
    {
        $this->errors[] = $this->module->l($msg);
        $this->redirectWithNotifications('index.php?controller=order&step=1');
    }
}
