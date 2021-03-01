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

use Sezzle\Model\CustomerOrder;
use Sezzle\Model\Session;

/**
 * Class SezzleTransaction.
 */
class SezzleTransaction extends ObjectModel
{
    /** @var int Sezzle Transaction Table ID */
    public $id_sezzle_transaction;

    /** @var string Prestashop Order Reference */
    public $reference;

    /** @var int Customer ID */
    public $id_customer;

    /** @var int Cart ID */
    public $id_cart;

    /** @var string Sezzle Transaction Order UUID */
    public $order_uuid;

    /** @var string Sezzle Checkout URL */
    public $checkout_url;

    /** @var float Sezzle Transaction Amount */
    public $checkout_amount;

    /** @var float Sezzle Transaction Auth Amount */
    public $authorized_amount;

    /** @var float Sezzle Transaction Capture Amount */
    public $capture_amount;

    /** @var float Sezzle Transaction Refund Amount */
    public $refund_amount;

    /** @var float Sezzle Transaction Release Amount */
    public $release_amount;

    /** @var string Sezzle Transaction Auth Expiration */
    public $auth_expiration;

    /** @var string Sezzle Transaction Payment Action */
    public $payment_action;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'sezzle_transaction',
        'primary' => 'id_sezzle_transaction',
        'multilang' => false,
        'fields' => array(
            'reference' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'order_uuid' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'checkout_url' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'checkout_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'authorized_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'capture_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'refund_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'release_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'auth_expiration' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'payment_action' => array('type' => self::TYPE_STRING, 'validate' => 'isString')
        ),
        'collation' => 'utf8_general_ci'
    );

    /**
     * @return int
     */
    public function getIdSezzleTransaction()
    {
        return $this->id_sezzle_transaction;
    }

    /**
     * @param int $id_sezzle_transaction
     * @return SezzleTransaction
     */
    public function setIdSezzleTransaction(int $id_sezzle_transaction)
    {
        $this->id_sezzle_transaction = $id_sezzle_transaction;
        return $this;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     * @return SezzleTransaction
     */
    public function setReference(string $reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdCustomer()
    {
        return $this->id_customer;
    }

    /**
     * @param int $id_customer
     * @return SezzleTransaction
     */
    public function setIdCustomer(int $id_customer)
    {
        $this->id_customer = $id_customer;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdCart()
    {
        return $this->id_cart;
    }

    /**
     * @param int $id_cart
     * @return SezzleTransaction
     */
    public function setIdCart(int $id_cart)
    {
        $this->id_cart = $id_cart;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderUUID()
    {
        return $this->order_uuid;
    }

    /**
     * @param string $order_uuid
     * @return SezzleTransaction
     */
    public function setOrderUUID(string $order_uuid)
    {
        $this->order_uuid = $order_uuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->checkout_url;
    }

    /**
     * @param string $checkout_url
     * @return SezzleTransaction
     */
    public function setCheckoutUrl(string $checkout_url)
    {
        $this->checkout_url = $checkout_url;
        return $this;
    }

    /**
     * @return float
     */
    public function getCheckoutAmount()
    {
        return $this->checkout_amount;
    }

    /**
     * @param float $checkout_amount
     * @return SezzleTransaction
     */
    public function setCheckoutAmount(float $checkout_amount)
    {
        $this->checkout_amount = $checkout_amount;
        return $this;
    }

    /**
     * @return float
     */
    public function getAuthorizedAmount()
    {
        return $this->authorized_amount;
    }

    /**
     * @param float $authorized_amount
     * @return SezzleTransaction
     */
    public function setAuthorizedAmount(float $authorized_amount)
    {
        $this->authorized_amount = $authorized_amount;
        return $this;
    }

    /**
     * @return float
     */
    public function getCaptureAmount()
    {
        return $this->capture_amount;
    }

    /**
     * @param float $capture_amount
     * @return SezzleTransaction
     */
    public function setCaptureAmount(float $capture_amount)
    {
        $this->capture_amount = $capture_amount;
        return $this;
    }

    /**
     * @return float
     */
    public function getRefundAmount()
    {
        return $this->refund_amount;
    }

    /**
     * @param float $refund_amount
     * @return SezzleTransaction
     */
    public function setRefundAmount(float $refund_amount)
    {
        $this->refund_amount = $refund_amount;
        return $this;
    }

    /**
     * @return float
     */
    public function getReleaseAmount()
    {
        return $this->release_amount;
    }

    /**
     * @param float $release_amount
     * @return SezzleTransaction
     */
    public function setReleaseAmount(float $release_amount)
    {
        $this->release_amount = $release_amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthExpiration()
    {
        return $this->auth_expiration;
    }

    /**
     * @param string $auth_expiration
     * @return SezzleTransaction
     */
    public function setAuthExpiration(string $auth_expiration)
    {
        $this->auth_expiration = $auth_expiration;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentAction()
    {
        return $this->payment_action;
    }

    /**
     * @param string $payment_action
     * @return SezzleTransaction
     */
    public function setPaymentAction(string $payment_action)
    {
        $this->payment_action = $payment_action;
        return $this;
    }

    /**
     * Store Checkout Session
     *
     * @param Cart $cart
     * @param Session $session
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws Exception
     */
    public function storeCheckoutSession(Cart $cart, Sezzle\Model\Session $session)
    {
        $this->setIdCustomer($cart->id_customer)
            ->setIdCart($cart->id)
            ->setCheckoutAmount($cart->getOrderTotal())
            ->setOrderUUID($session->getOrder()->getUuid())
            ->setCheckoutUrl($session->getOrder()->getCheckoutUrl())
            ->setPaymentAction(Configuration::get(Sezzle::$formFields['payment_action']))
            ->save();
    }

    /**
     * Store Tokenized Order
     *
     * @param Cart $cart
     * @param CustomerOrder $order
     * @throws PrestaShopException
     */
    public function storeTokenizedOrder(Cart $cart, CustomerOrder $order)
    {
        $this->setIdCustomer($cart->id_customer)
            ->setIdCart($cart->id)
            ->setCheckoutAmount($cart->getOrderTotal())
            ->setOrderUUID($order->getUuid())
            ->setPaymentAction(Configuration::get(Sezzle::$formFields['payment_action']))
            ->save();
    }

    /**
     * Get Checkout Session
     *
     * @param int $cartId
     * @return SezzleTransaction
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getByCartId($cartId)
    {
        $sql = new DbQuery();
        $sql->select(self::$definition['primary'])
            ->from(self::$definition['table'])
            ->where('id_cart = ' . (int)$cartId)
            ->orderBy(self::$definition['primary'] . " DESC");
        $idSezzleTransaction = Db::getInstance()->getValue($sql);
        return new self($idSezzleTransaction);
    }

    /**
     * Get Checkout Session
     *
     * @param string $reference
     * @return SezzleTransaction
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getByReference($reference)
    {
        $sql = new DbQuery();
        $sql->select(self::$definition['primary'])
            ->from(self::$definition['table'])
            ->where(sprintf('reference = "%s"', pSQL($reference)))
            ->orderBy(self::$definition['primary'] . " DESC");
        $idSezzleTransaction = Db::getInstance()->getValue($sql);
        return new self($idSezzleTransaction);
    }

    /**
     * Store Authorize Amount
     *
     * @param float $amount
     * @param string $orderUUID
     */
    public static function storeAuthorizeAmount($amount, $orderUUID)
    {
        Db::getInstance()->update(
            self::$definition['table'],
            array(
                'authorized_amount' => (float)$amount,
            ),
            sprintf('order_uuid = "%s"', pSQL($orderUUID))
        );
    }

    /**
     * Store Capture Amount
     *
     * @param float $amount
     * @param string $orderUUID
     */
    public static function storeCaptureAmount($amount, $orderUUID)
    {
        Db::getInstance()->update(
            self::$definition['table'],
            array(
                'capture_amount' => (float)$amount,
            ),
            sprintf('order_uuid = "%s"', pSQL($orderUUID))
        );
    }

    /**
     * Store Release Amount
     *
     * @param float $amount
     * @param string $orderUUID
     */
    public static function storeReleaseAmount($amount, $orderUUID)
    {
        Db::getInstance()->update(
            self::$definition['table'],
            array(
                'release_amount' => (float)$amount,
            ),
            sprintf('order_uuid = "%s"', pSQL($orderUUID))
        );
    }

    /**
     * Store Refund Amount
     *
     * @param float $amount
     * @param string $orderUUID
     */
    public static function storeRefundAmount($amount, $orderUUID)
    {
        Db::getInstance()->update(
            self::$definition['table'],
            array(
                'refund_amount' => (float)$amount,
            ),
            sprintf('order_uuid = "%s"', pSQL($orderUUID))
        );
    }

    /**
     * Store Prestashop Order Reference
     *
     * @param string $reference
     * @param string $orderUUID
     */
    public static function storeOrderReference($reference, $orderUUID)
    {
        Db::getInstance()->update(
            self::$definition['table'],
            array(
                'reference' => pSQL($reference),
            ),
            sprintf('order_uuid = "%s"', pSQL($orderUUID))
        );
    }

    /**
     * Store Prestashop Auth Expiration
     *
     * @param string $expiration
     * @param string $orderUUID
     */
    public static function storeAuthExpiration($expiration, $orderUUID)
    {
        Db::getInstance()->update(
            self::$definition['table'],
            array(
                'auth_expiration' => pSQL($expiration),
            ),
            sprintf('order_uuid = "%s"', pSQL($orderUUID))
        );
    }
}
