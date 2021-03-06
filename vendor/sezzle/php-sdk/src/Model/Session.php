<?php

namespace Sezzle\Model;

use Sezzle\Model\Session\Customer;
use Sezzle\Model\Session\Order;
use Sezzle\Model\Session\Tokenize;
use Sezzle\Model\Session\Url;

class Session
{
    /**
     * @var Url
     */
    private $cancelUrl;

    /**
     * @var Url
     */
    private $completeUrl;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var Tokenize
     */
    private $tokenize;

    /**
     * @return Url
     */
    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    /**
     * @param Url $cancelUrl
     * @return Session
     */
    public function setCancelUrl(Url $cancelUrl)
    {
        $this->cancelUrl = $cancelUrl;
        return $this;
    }

    /**
     * @return Url
     */
    public function getCompleteUrl()
    {
        return $this->completeUrl;
    }

    /**
     * @param Url $completeUrl
     * @return Session
     */
    public function setCompleteUrl(Url $completeUrl)
    {
        $this->completeUrl = $completeUrl;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @return Session
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Order $order
     * @return Session
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return Tokenize
     */
    public function getTokenize()
    {
        return $this->tokenize;
    }

    /**
     * @param Tokenize $tokenize
     * @return Session
     */
    public function setTokenize(Tokenize $tokenize)
    {
        $this->tokenize = $tokenize;
        return $this;
    }

    /**
     * @param array $data
     * @return Session
     */
    public static function fromArray(array $data = [])
    {
        $result = new self();

//        $result->setCancelUrl($data['cancel_url']);
//        $result->setCompleteUrl($data['complete_url']);
//        $result->setCustomer($data['customer']);

        if (array_key_exists('order', $data)) {
            $result->setOrder(Order::fromArray($data['order']));
        }

        if (array_key_exists('tokenize', $data)) {
            $result->setTokenize(Tokenize::fromArray($data['tokenize']));
        }


        return $result;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'cancel_url' => $this->getCancelUrl()->toArray(),
            'complete_url' => $this->getCompleteUrl()->toArray(),
            'customer' => $this->getCustomer()->toArray(),
            'order' => $this->getOrder()->toArray()
        ];
    }
}
