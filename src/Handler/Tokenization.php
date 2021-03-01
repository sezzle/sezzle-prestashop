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

namespace PrestaShop\Module\Sezzle\Handler;

use Cart;
use Currency;
use DateTime;
use Exception;
use PrestaShop\Module\Sezzle\Handler\Service\Tokenization as TokenizationServiceHandler;
use PrestaShop\Module\Sezzle\Handler\Service\Util;
use PrestaShopException;
use Sezzle\HttpClient\RequestException;
use Sezzle\Model\CustomerOrder;
use SezzleTokenization;
use SezzleTransaction;

/**
 * Class Tokenization
 * @package PrestaShop\Module\Sezzle\Handler
 */
class Tokenization
{

    /**
     * Store Tokenization Records
     *
     * @param int $customerId
     * @param string $token
     * @throws PrestaShopException
     * @throws RequestException
     */
    public static function storeTokenizationRecords($customerId, $token)
    {
        $tokenizationDetails = TokenizationServiceHandler::getTokenizationDetails($token);
        $tokenizeTxn = new SezzleTokenization();
        $tokenizeTxn->storeCustomerTokenRecord($customerId, $tokenizationDetails);
    }

    /**
     * Get Customer UUID
     *
     * @param int $customerId
     * @return string|null
     * @throws Exception
     */
    public static function getCustomerUUID($customerId)
    {
        $tokenizeTxn = SezzleTokenization::getByCustomerId($customerId);
        $dateTimeNow = new DateTime();
        $dateTimeExpire = new DateTime($tokenizeTxn->getCustomerUuidExpiration());
        if ($dateTimeNow > $dateTimeExpire) {
            SezzleTokenization::deleteTokenizationRecord($customerId);
            return null;
        }
        return $tokenizeTxn->getCustomerUuid();
    }



    /**
     * Create Order by Customer UUID
     *
     * @param string $customerUUID
     * @param Cart $cart
     * @return CustomerOrder
     * @throws RequestException
     * @throws Exception
     */
    public function createOrder($customerUUID, $cart)
    {
        $payload = $this->buildCustomerOrderPayload($cart);
        return TokenizationServiceHandler::createOrder($customerUUID, $payload);
    }

    /**
     * Build Customer Order Payload
     *
     * @return CustomerOrder
     * @throws Exception
     */
    private function buildCustomerOrderPayload($cart)
    {
        $payload = new CustomerOrder();
        $currency = new Currency($cart->id_currency);
        $amountInCents = \Sezzle\Util::formatToCents($cart->getOrderTotal());
        $orderAmount = Util::getAmountObject($amountInCents, $currency->iso_code);
        return $payload->setIntent('AUTH')
            ->setReferenceId(strval($cart->id))
            ->setOrderAmount($orderAmount);
    }
}
