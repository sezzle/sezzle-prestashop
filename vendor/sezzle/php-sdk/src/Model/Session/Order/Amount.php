<?php

namespace Sezzle\Model\Session\Order;

class Amount
{
    /**
     * @var int
     */
    private $amountInCents;

    /**
     * @var string
     */
    private $currency;

    /**
     * @return int
     */
    public function getAmountInCents()
    {
        return $this->amountInCents;
    }

    /**
     * @param int $amountInCents
     * @return Amount
     */
    public function setAmountInCents($amountInCents)
    {
        $this->amountInCents = $amountInCents;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return Amount
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param array|null $data
     * @return Amount
     */
    public static function fromArray(array $data = null)
    {
        $result = new self();

        if ($data === null) {
            return $result;
        }

        $result->setAmountInCents($data['amount_in_cents']);
        $result->setCurrency($data['currency']);

        return $result;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'amount_in_cents' => $this->getAmountInCents(),
            'currency' => $this->getCurrency(),
        ];
    }
}
