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

use Sezzle\HttpClient\RequestException;
use PrestaShop\Module\Sezzle\Handler\Service\Order as OrderService;
use Sezzle\Model\CustomerOrder;
use Sezzle\Model\Session;

/**
 * Class SezzleAbstractModuleFrontController
 */
abstract class SezzleAbstractModuleFrontController extends ModuleFrontController
{
    /**
     * @var \Sezzle\Model\Order
     */
    public $sezzleOrder = null;

    /**
     * Validate Checkout
     *
     * @param SezzleTransaction $sezzleTransaction
     * @param bool $postRedirect
     * @return bool
     * @throws Exception
     */
    public function isCheckoutValid(SezzleTransaction $sezzleTransaction = null, $postRedirect = false)
    {
        $cart = $this->context->cart;
        $customer = new Customer((int)$cart->id_customer);

        if ($cart->secure_key !== $customer->secure_key
            || $cart->id_customer == 0
            || $cart->id_address_delivery == 0
            || $cart->id_address_invoice == 0
            || !$this->module->active
            || empty($this->context->cart->getProducts())) {
            return false;
        }

        if ($postRedirect &&
            (
                !$sezzleTransaction->getIdSezzleTransaction()
                || !$this->isCheckoutApproved($sezzleTransaction->getOrderUUID())
                || !$this->isAmountMatched($sezzleTransaction->getCheckoutAmount(), $cart->getOrderTotal())
            )
        ) {
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
     * Checking if the checkout was approved by Sezzle
     *
     * @param string $orderUUID
     * @return bool
     * @throws RequestException
     */
    private function isCheckoutApproved($orderUUID)
    {
        if (!$orderUUID) {
            return false;
        }
        $sezzleOrder = OrderService::getOrder($orderUUID);
        if (!$sezzleOrder->getAuthorization() || !$sezzleOrder->getAuthorization()->isApproved()) {
            return false;
        }
        $this->sezzleOrder = $sezzleOrder;
        return true;
    }

    public function setAuthorizedAmount()
    {
    }

    /**
     * Handle Error
     *
     * @param string $msg
     */
    public function handleError($msg = "")
    {
        if ($msg) {
            $this->errors[] = $this->module->l($msg);
        }
        $this->redirectWithNotifications('index.php?controller=order&step=1');
    }

    /**
     * Post Checkout Session Creation
     *
     * @param Session $session
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function postCheckoutSessionCreation(Session $session)
    {
        $cart = $this->context->cart;
        $txn = new SezzleTransaction();
        $txn->storeCheckoutSession($cart, $session);
        $this->context->cookie->token = $session->getTokenize()->getToken();
    }

    /**
     * Post Tokenized Order Creation
     *
     * @param CustomerOrder $order
     * @throws PrestaShopException
     */
    public function postTokenizedOrderCreation(CustomerOrder $order)
    {
        $cart = $this->context->cart;
        $txn = new SezzleTransaction();
        $txn->storeTokenizedOrder($cart, $order);
    }
}
