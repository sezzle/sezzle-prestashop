<?php


namespace Sezzle\Model;

use Sezzle\Model\Order\Authorization;
use Sezzle\Model\Order\Links;
use Sezzle\Model\Session\Order\Amount;

class Order
{
    /**
     * @var string
     */
    public $uuid;
    /**
     * @var Links[]
     */
    public $links; //array(Link)
    /**
     * @var string
     */
    public $intent; //String
    /**
     * @var string
     */
    public $expiration; //Date
    /**
     * @var string
     */
    public $referenceId; //String
    /**
     * @var string
     */
    public $description; //String
    /**
     * @var Amount
     */
    public $orderAmount; //OrderAmount
    /**
     * @var Authorization
     */
    public $authorization; //Authorization

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }


    /**
     * @return Links[]
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param Links[] $links
     */
    public function setLinks(array $links)
    {
        $this->links = $links;
    }


    /**
     * @return string
     */
    public function getIntent()
    {
        return $this->intent;
    }

    /**
     * @param string $intent
     */
    public function setIntent($intent)
    {
        $this->intent = $intent;
    }


    /**
     * @return string
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * @param string $expiration
     */
    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;
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
     */
    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;
    }


    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     */
    public function setOrderAmount(Amount $orderAmount)
    {
        $this->orderAmount = $orderAmount;
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
     */
    public function setAuthorization(Authorization $authorization)
    {
        $this->authorization = $authorization;
    }

    /**
     * @param array $data
     * @return Order
     */
    public static function fromArray(array $data = [])
    {
        $result = new self();

        if (array_key_exists('order_amount', $data)) {
            $result->setOrderAmount(Amount::fromArray($data['order_amount']));
        }
        $result->setUuid($data['uuid']);
        $result->setIntent($data['intent']);
        $result->setReferenceId($data['reference_id']);

        if (array_key_exists('description', $data)) {
            $result->setDescription($data['description']);
        }
        if (array_key_exists('authorization', $data)) {
            $result->setAuthorization(Authorization::fromArray($data['authorization']));
        }
        if (array_key_exists('expiration', $data)) {
            $result->setExpiration($data['expiration']);
        }

        $links = [];
        foreach ($data['links'] as $link) {
            $links[] = Links::fromArray($link);
        }
        $result->setLinks($links);


        return $result;
    }
}
