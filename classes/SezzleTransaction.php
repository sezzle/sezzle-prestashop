<?php

use Sezzle\Model\Session;
use Sezzle\Util;

/**
 * Class SezzleTransaction.
 */
class SezzleTransaction extends ObjectModel
{
    /** @var int Sezzle Transaction Table ID */
    public $id_sezzle_transaction;

    /** @var string Prestashop Order Reference */
    public $reference;

    /** @var int Cart ID */
    public $id_cart;

    /** @var string Sezzle Transaction Order UUID */
    public $order_uuid;

    /** @var string Sezzle Checkout URL */
    public $checkout_url;

    /** @var string Sezzle Checkout Expiration */
    public $checkout_expiration;

    /** @var float Sezzle Transaction Auth Amount */
    public $authorized_amount;

    /** @var float Sezzle Transaction Capture Amount */
    public $capture_amount;

    /** @var float Sezzle Transaction Refund Amount */
    public $refund_amount;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'sezzle_transaction',
        'primary' => 'id_sezzle_transaction',
        'multilang' => false,
        'fields' => array(
            'reference' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'order_uuid' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'checkout_url' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'checkout_expiration' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'authorized_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'capture_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'refund_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
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
    public function getOrderUuid()
    {
        return $this->order_uuid;
    }

    /**
     * @param string $order_uuid
     * @return SezzleTransaction
     */
    public function setOrderUuid(string $order_uuid)
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
     * @return string
     */
    public function getCheckoutExpiration()
    {
        return $this->checkout_expiration;
    }

    /**
     * @param string $checkout_expiration
     * @return SezzleTransaction
     */
    public function setCheckoutExpiration(string $checkout_expiration)
    {
        $this->checkout_expiration = $checkout_expiration;
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
        $this->setIdCart($cart->id)
            ->setAuthorizedAmount($cart->getOrderTotal())
            ->setOrderUuid($session->getOrder()->getUuid())
            ->setCheckoutUrl($session->getOrder()->getCheckoutUrl())
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
            ->where('id_cart = ' . (int)$cartId);
        $idSezzleTransaction = Db::getInstance()->getValue($sql);
        return new self($idSezzleTransaction);
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
            'order_uuid = ' . $orderUUID
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
                'reference' => $reference,
            ),
            'order_uuid = ' . $orderUUID
        );
    }
}
