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
use PrestaShop\Module\Sezzle\Handler\Order;
use Sezzle;
use SezzleTransaction;

/**
 * Class Authorization
 * @package PrestaShop\Module\Sezzle\Handler\Payment
 */
class Authorization extends Order
{

    /**
     * Authorization Action
     *
     * @param string $orderUUID
     * @param Sezzle\Model\Order\Authorization $authorization
     */
    public function execute($orderUUID, $authorization)
    {
        $amount = $authorization->getAuthorizationAmount()->getAmountInCents();
        if ($amount <= 0) {
            return;
        }
        $authorizedAmount = (float)$amount / 100;
        SezzleTransaction::storeAuthorizeAmount($authorizedAmount, $orderUUID);
        if (Configuration::get(Sezzle::$formFields['payment_action']) === Sezzle::ACTION_AUTHORIZE) {
            SezzleTransaction::storeAuthExpiration($authorization->getExpiration(), $orderUUID);
        }
    }
}
