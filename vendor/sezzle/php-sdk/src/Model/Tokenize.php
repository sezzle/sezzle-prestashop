<?php

namespace Sezzle\Model;

use Sezzle\Model\Tokenize\Customer;

class Tokenize
{

    /**
     * @var string
     */
    private $token;

    /**
     * Scopes expressed in the form of resource URL endpoints. The value of the scope parameter
     * is expressed as a list of space-delimited, case-sensitive strings.
     *
     * @var Customer
     */
    private $customer;

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return Tokenize
     */
    public function setToken($token)
    {
        $this->token = $token;
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
     * @return Tokenize
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
        return $this;
    }


    /**
     * @param array $data
     * @return Tokenize
     */
    public static function fromArray(array $data)
    {
        $result = new self();

        if (array_key_exists('token', $data)) {
            $result->setToken($data['token']);
        }

        if (array_key_exists('customer', $data)) {
            $result->setCustomer(Customer::fromArray($data['customer']));
        }

        return $result;
    }
}
