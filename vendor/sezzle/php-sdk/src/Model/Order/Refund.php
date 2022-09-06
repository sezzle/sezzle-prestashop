<?php


namespace Sezzle\Model\Order;

use Sezzle\Model\Session;
use Sezzle\Model\Session\Order;
use Sezzle\Model\Session\Order\Amount;

/**
 * Class Refund
 * @package Sezzle\Model\Order
 */
class Refund
{
    /**
     * @var string
     */
    public $uuid;

    /**
     * @var Amount
     */
    public $refundAmount;

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     * @return Refund
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return Amount
     */
    public function getRefundAmount()
    {
        return $this->refundAmount;
    }

    /**
     * @param Amount $refundAmount
     * @return Refund
     */
    public function setRefundAmount(Amount $refundAmount)
    {
        $this->refundAmount = $refundAmount;
        return $this;
    }

    /**
     * @param array $data
     * @return Refund
     */
    public static function fromArray(array $data = [])
    {
        $result = new self();

        $result->setUuid($data['uuid']);

        return $result;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getRefundAmount()->toArray();
    }
}
