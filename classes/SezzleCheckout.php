<?php

use Sezzle\Model\Session;

/**
 * Class SezzleCheckout.
 */
class SezzleCheckout extends ObjectModel
{
    /** @var int Sezzle Checkout Table ID */
    public $id_sezzle_checkout;

    /** @var string Prestashop Order Reference */
    public $reference;

    /** @var int Cart ID */
    public $id_cart;

    /** @var string Sezzle Checkout Order UUID */
    public $order_uuid;

    /** @var string Sezzle Checkout URL */
    public $checkout_url;

    /** @var string Sezzle Checkout Expiration */
    public $checkout_expiration;

    /** @var int Sezzle Checkout Order Amount */
    public $order_amount;

    /** @var int Sezzle Checkout Capture Amount */
    public $capture_amount;

    /** @var int Sezzle Checkout Refund Amount */
    public $refund_amount;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'sezzle_checkout',
        'primary' => 'id_sezzle_checkout',
        'multilang' => false,
        'fields' => array(
            'id_sezzle_checkout' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'reference' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'order_uuid' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'checkout_url' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'checkout_expiration' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'order_amount' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'capture_amount' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'refund_amount' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
        ),
        'collation' => 'utf8_general_ci'
    );

    /**
     * Store Checkout Session
     *
     * @param Session $session
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function storeCheckoutSession(Sezzle\Model\Session $session)
    {
        $this->order_amount = $session->getOrder()->getOrderAmount()->getAmountInCents();
        $this->order_uuid = $session->getOrder()->getUuid();
        $this->checkout_url = $session->getOrder()->getCheckoutUrl();
        $this->add();
    }

    /**
     * Get Checkout Session
     *
     * @param int $cartId
     * @return SezzleCapture
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getCheckoutSessionByCartId($cartId)
    {
        $sql = new DbQuery();
        $sql->select('id_sezzle_checkout');
        $sql->from('sezzle_checkout');
        $sql->where('id_cart = ' . (int)$cartId);
        $idSezzleCheckout = Db::getInstance()->getValue($sql);
        return new self($idSezzleCheckout);
    }
}
