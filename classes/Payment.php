<?php
/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Sezzle <dev@sezzle.com>
 * @copyright Copyright (c) Sezzle
 * @license   https://www.apache.org/licenses/LICENSE-2.0.txt  Apache 2.0 License
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

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
            sprintf(
                'order_reference = "%s" ORDER BY %s DESC',
                pSQL($orderReference),
                pSQL(OrderPayment::$definition['primary'])
            ),
            1
        );
    }

    /**
     * Set Transaction Id in Order Payment
     *
     * @param string $orderReference
     */
    public static function deletePayment($orderReference)
    {
        Db::getInstance()->delete(
            OrderPayment::$definition['table'],
            sprintf(
                'order_reference = "%s" ORDER BY %s DESC',
                pSQL($orderReference),
                OrderPayment::$definition['primary']
            )
        );
    }

    /**
     * Delete Refund Transaction
     *
     * @param int $orderId
     */
    public static function deleteRefund($orderId)
    {
        Db::getInstance()->delete(
            OrderSlip::$definition['table'],
            sprintf(
                'id_order = %s ORDER BY %s DESC',
                (int)$orderId,
                OrderSlip::$definition['primary']
            )
        );
    }
}
