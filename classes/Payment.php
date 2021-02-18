<?php


/**
 * Class Payment.
 */
class Payment
{

    /**
     * Set Transaction Id in Order Payment
     *
     * @param string $orderReference
     * @param string $transactionId
     */
    public static function setTransactionId($orderReference, $transactionId)
    {
        Db::getInstance()->update(
            OrderPayment::$definition['table'],
            array(
                'transaction_id' => pSQL($transactionId),
            ),
            sprintf('order_reference = "%s"', pSQL($orderReference))
        );
    }
}
