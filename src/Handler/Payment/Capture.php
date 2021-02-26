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

use Configuration;
use Currency;
use DateTime;
use Exception;
use Payment;
use OrderCore as CoreOrder;
use PrestaShop\Module\Sezzle\Handler\Order;
use PrestaShop\Module\Sezzle\Handler\Service\Capture as CaptureServiceHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShopException;
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
     * @throws OrderException
     * @throws Exception
     */
    public function execute($amount)
    {
        if ($amount <= 0) {
            throw new OrderException("Invalid capture amount.");
        } elseif ($amount > $this->order->total_paid) {
            throw new OrderException("Capture amount is greater than the order amount.");
        }

        $txn = SezzleTransaction::getByCartId($this->order->id_cart);
        if ($authExpiration = $txn->getAuthExpiration()) {
            $dateTimeNow = new DateTime();
            $dateTimeExpire = new DateTime($authExpiration);
            if ($dateTimeNow > $dateTimeExpire) {
                throw new OrderException("Cannot process payment. Auth Expired.");
            }
        }

        $isPartial = $amount < $this->order->total_paid;
        $currency = new Currency($this->order->id_currency);
        // capture action
        $response = CaptureServiceHandler::capturePayment(
            $txn->getOrderUUID(),
            $amount,
            $currency->iso_code,
            $isPartial
        );
        if ($captureUuid = $response->getUuid()) {
            $finalAmount = $amount + $txn->getCaptureAmount();
            Payment::setTransactionId($this->order->reference, $captureUuid);
            SezzleTransaction::storeCaptureAmount($finalAmount, $txn->getOrderUUID());
            if (!$isPartial) {
                $this->changeOrderState($this->order, Configuration::get('PS_OS_PAYMENT'));
            }
        }
    }
}
