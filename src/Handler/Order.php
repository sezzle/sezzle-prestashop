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

use PrestaShopException;
use OrderCore as CoreOrder;

/**
 * Class Order
 * @package PrestaShop\Module\Sezzle\Handler
 */
class Order
{

    /**
     * Change Order State
     *
     * @param CoreOrder $order
     * @param int $state
     * @throws PrestaShopException
     */
    public static function changeOrderState(CoreOrder $order, $state)
    {
        if (!$state) {
            return;
        }
        $order->setCurrentState($state);
        $order->save();
    }
}
