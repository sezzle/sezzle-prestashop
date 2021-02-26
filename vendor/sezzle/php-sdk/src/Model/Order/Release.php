<?php


namespace Sezzle\Model\Order;

use Sezzle\Model\Session;
use Sezzle\Model\Session\Order;
use Sezzle\Model\Session\Order\Amount;

/**
 * Class Release
 * @package Sezzle\Model\Order
 */
class Release
{
    /**
     * @var string
     */
    public $uuid;

    /**
     * @var Amount
     */
    public $releaseAmount;

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     * @return Release
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return Amount
     */
    public function getReleaseAmount()
    {
        return $this->releaseAmount;
    }

    /**
     * @param Amount $releaseAmount
     * @return Release
     */
    public function setReleaseAmount(Amount $releaseAmount)
    {
        $this->releaseAmount = $releaseAmount;
        return $this;
    }

    /**
     * @param array $data
     * @return Release
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
        return $this->getReleaseAmount()->toArray();
    }
}
