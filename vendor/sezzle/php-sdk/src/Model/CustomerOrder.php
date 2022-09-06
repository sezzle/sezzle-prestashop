<?php

namespace Sezzle\Model;

use Sezzle\Model\Order\Authorization;
use Sezzle\Model\Session\Order\Amount;

class CustomerOrder
{
    /**
     * @var string
     */
    private $intent;
    /**
     * @var string
     */
    private $referenceId;
    /**
     * @var Amount
     */
    private $orderAmount;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var Authorization
     */
    private $authorization;

    /**
     * @return string
     */
    public function getIntent()
    {
        return $this->intent;
    }

    /**
     * @param string $intent
     * @return CustomerOrder
     */
    public function setIntent($intent)
    {
        $this->intent = $intent;
        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceId()
    {
        return $this->referenceId;
    }

    /**
     * @param string $referenceId
     * @return CustomerOrder
     */
    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;
        return $this;
    }

    /**
     * @return Amount
     */
    public function getOrderAmount()
    {
        return $this->orderAmount;
    }

    /**
     * @param Amount $orderAmount
     * @return CustomerOrder
     */
    public function setOrderAmount(Amount $orderAmount)
    {
        $this->orderAmount = $orderAmount;
        return $this;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     * @return CustomerOrder
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return Authorization
     */
    public function getAuthorization()
    {
        return $this->authorization;
    }

    /**
     * @param Authorization $authorization
     * @return CustomerOrder
     */
    public function setAuthorization(Authorization $authorization)
    {
        $this->authorization = $authorization;
        return $this;
    }


    /**
     * @param array $data
     * @return CustomerOrder
     */
    public static function fromArray(array $data)
    {
        $result = new self();

        $result->setUuid($data['uuid']);
        $result->setIntent($data['intent']);
        $result->setReferenceId($data['reference_id']);

        if (array_key_exists('authorization', $data)) {
            $result->setAuthorization(Authorization::fromArray($data['authorization']));
        }

        if (array_key_exists('order_amount', $data)) {
            $result->setOrderAmount(Amount::fromArray($data['order_amount']));
        }
        return $result;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'intent' => $this->getIntent(),
            'reference_id' => $this->getReferenceId(),
            'order_amount' => $this->getOrderAmount()->toArray()
        ];
    }
}
