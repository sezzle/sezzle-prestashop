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

namespace PrestaShop\Module\Sezzle\Handler\Payment;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Configuration;
use Currency;
use DateTime;
use Exception;
use Payment;
use OrderCore as CoreOrder;
use PrestaShop\Module\Sezzle\Handler\Order;
use PrestaShop\Module\Sezzle\Handler\Service\Capture as CaptureServiceHandler;
use PrestaShop\Module\Sezzle\Handler\Service\Util;
use PrestaShopException;
use Sezzle;
use Sezzle\HttpClient\RequestException;
use SezzleTransaction;

/**
 * Class Capture
 * @package PrestaShop\Module\Sezzle\Handler\Payment
 */
class Capture extends Order
{
    /**
     * @var CoreOrder
     */
    private $order;

    /**
     * Capture constructor.
     * @param CoreOrder $order
     */
    public function __construct(CoreOrder $order)
    {
        $this->order = $order;
    }

    /**
     * Capture Action
     *
     * @param float $amount
     * @throws RequestException
     * @throws PrestaShopException
     * @throws Exception
     */
    public function execute($amount)
    {
        if ($amount <= 0) {
            throw new PrestaShopException("Invalid capture amount.");
        } elseif ($amount > $this->order->total_paid) {
            throw new PrestaShopException("Capture amount is greater than the order amount.");
        }

        // auth expiry check
        $txn = SezzleTransaction::getByCartId($this->order->id_cart);
        if ($txn->getPaymentAction() === Sezzle::ACTION_AUTHORIZE
            && $txn->getAuthExpiration() != 0) {
            $dateTimeNow = new DateTime();
            $dateTimeExpire = new DateTime($txn->getAuthExpiration());
            if ($dateTimeNow > $dateTimeExpire) {
                throw new PrestaShopException("Cannot process payment. Auth Expired.");
            }
        }

        $isPartial = $amount < $this->order->total_paid;
        $payload = $this->buildCapturePayload($amount, $isPartial);
        // capture action
        $response = CaptureServiceHandler::capturePayment(
            $txn->getOrderUUID(),
            $payload
        );
        if ($captureUuid = $response->getUuid()) {
            // post capture handling
            $finalAmount = $amount + $txn->getCaptureAmount();
            SezzleTransaction::storeCaptureAmount($finalAmount, $txn->getOrderUUID());
            if (!$isPartial) {
                $this->changeOrderState($this->order, Configuration::get('PS_OS_PAYMENT'));
            }
            Payment::setTransactionId($this->order->reference, $captureUuid);
        }
    }

    /**
     * Build Capture Payload
     *
     * @param float $amount
     * @param bool $isPartial
     * @return Sezzle\Model\Order\Capture
     */
    public function buildCapturePayload($amount, $isPartial)
    {
        $currency = new Currency($this->order->id_currency);
        $captureModel = new Sezzle\Model\Order\Capture();
        return $captureModel->setCaptureAmount(
            Util::getAmountObject(
                Sezzle\Util::formatToCents($amount),
                $currency->iso_code
            )
        )
            ->setPartialCapture($isPartial);
    }

    /**
     * Remove Capture Transaction
     *
     * @param string $orderReference
     */
    public static function removeCaptureTransaction($orderReference)
    {
        Payment::deletePayment($orderReference);
    }
}
