<?php

namespace Sezzle\Model\Session\Order;

class Item
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $sku;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var Amount
     */
    private $price;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Item
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     * @return Item
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return Item
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return Amount
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param Amount $price
     * @return Item
     */
    public function setPrice(Amount $price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @param array|null $data
     * @return Item
     */
    public static function fromArray(array $data = null)
    {
        $result = new self();

        if ($data === null) {
            return $result;
        }

        $result->setName($data['name']);
        $result->setSku($data['sku']);
        $result->setQuantity($data['quantity']);
        $result->setPrice($data['price']);

        return $result;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'sku' => $this->getSku(),
            'quantity' => $this->getQuantity(),
            'price' => $this->getPrice()->toArray(),
        ];
    }
}
