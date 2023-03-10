<?php


namespace Sezzle\Model\Order;

use Sezzle\Model\Session;
use Sezzle\Model\Session\Order;
use Sezzle\Model\Session\Order\Amount;

/**
 * Class Capture
 * @package Sezzle\Model\Order
 */
class Capture
{
    /**
     * @var string
     */
    public $uuid;

    /**
     * @var Amount
     */
    public $captureAmount;
    /**
     * @var bool
     */
    public $partialCapture;

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
     * @return Amount
     */
    public function getCaptureAmount()
    {
        return $this->captureAmount;
    }

    /**
     * @param Amount $captureAmount
     * @return Capture
     */
    public function setCaptureAmount(Amount $captureAmount)
    {
        $this->captureAmount = $captureAmount;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPartialCapture()
    {
        return $this->partialCapture;
    }

    /**
     * @param bool $partialCapture
     * @return Capture
     */
    public function setPartialCapture($partialCapture)
    {
        $this->partialCapture = $partialCapture;
        return $this;
    }

    /**
     * @param array $data
     * @return Capture
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
        return [
            'capture_amount' => $this->getCaptureAmount()->toArray(),
            'partial_capture' => $this->isPartialCapture()
        ];
    }
}
