<?php

namespace Sezzle\Model\Session;

use Sezzle\Model\Session\Customer\Address;

class Customer
{
    /**
     * @var bool
     */
    private $tokenize;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $dob;

    /**
     * @var Address
     */
    private $billingAddress;

    /**
     * @var Address
     */
    private $shippingAddress;

    /**
     * @return bool
     */
    public function isTokenize()
    {
        return $this->tokenize;
    }

    /**
     * @param bool $tokenize
     * @return Customer
     */
    public function setTokenize($tokenize)
    {
        $this->tokenize = $tokenize;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return Customer
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return Customer
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return Customer
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * @param string $dob
     * @return Customer
     */
    public function setDob($dob)
    {
        $this->dob = $dob;
        return $this;
    }

    /**
     * @return Address
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @param Address $billingAddress
     * @return Customer
     */
    public function setBillingAddress(Address $billingAddress)
    {
        $this->billingAddress = $billingAddress;
        return $this;
    }

    /**
     * @return Address
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * @param Address $shippingAddress
     * @return Customer
     */
    public function setShippingAddress(Address $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
        return $this;
    }

    /**
     * @param array|null $data
     * @return Customer
     */
    public static function fromArray(array $data = null)
    {
        $result = new self();

        if ($data === null) {
            return $result;
        }

        $result->setTokenize($data['tokenize']);
        $result->setEmail($data['email']);
        $result->setFirstName($data['first_name']);
        $result->setLastName($data['last_name']);
        $result->setPhone($data['phone']);
        $result->setDob($data['dob']);
        $result->setBillingAddress($data['billing_address']);
        $result->setShippingAddress($data['shipping_address']);

        return $result;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'tokenize' => $this->isTokenize(),
            'email' => $this->getEmail(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'phone' => $this->getPhone(),
            'dob' => $this->getDob(),
            'billing_address' => $this->getBillingAddress()->toArray(),
            'shipping_address' => $this->getShippingAddress()->toArray()
        ];
    }
}
