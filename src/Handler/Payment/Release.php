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

use Currency;
use Payment;
use OrderCore as CoreOrder;
use PrestaShop\Module\Sezzle\Handler\Order;
use PrestaShop\Module\Sezzle\Handler\Service\Release as ReleaseServiceHandler;
use PrestaShop\Module\Sezzle\Handler\Service\Util;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidRefundException;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Exception;
use PrestaShopException;
use Sezzle;
use Sezzle\HttpClient\RequestException;
use SezzleTransaction;

/**
 * Class Release
 * @package PrestaShop\Module\Sezzle\Handler\Payment
 */
class Release extends Order
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
     * Release Action
     *
     * @throws PrestaShopException
     * @throws RequestException
     */
    public function execute()
    {
        $payments = $this->order->getOrderPayments();
        if ($this->order->payment !== Sezzle::DISPLAY_NAME) {
            return;
        } elseif (count($payments) > 0) {
            throw new PrestaShopException("Cannot release. Payment already processed.");
        }

        $txn = SezzleTransaction::getByReference($this->order->reference);
        if ($txn->getCaptureAmount() > 0 || $txn->getReleaseAmount() === $txn->getAuthorizedAmount()) {
            throw new PrestaShopException("Invalid amount.");
        }

        $releasePayload = $this->buildReleasePayload();
        // release action
        $response = ReleaseServiceHandler::releasePayment(
            $txn->getOrderUUID(),
            $releasePayload
        );
        if ($response->getUuid()) {
            SezzleTransaction::storeReleaseAmount($this->order->total_paid, $txn->getOrderUUID());
        }
    }

    /**
     * Build Release Payload
     *
     * @return Sezzle\Model\Session\Order\Amount
     */
    public function buildReleasePayload()
    {
        $currency = new Currency($this->order->id_currency);
        return Util::getAmountObject(
            Sezzle\Util::formatToCents($this->order->total_paid),
            $currency->iso_code
        );
    }
}
