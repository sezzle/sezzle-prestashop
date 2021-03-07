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

use Context;
use Sezzle;
use Tools;

/**
 * Class Util
 * @package PrestaShop\Module\Sezzle\Handler
 */
class Util
{
    /**
     * Round Amount
     *
     * @param float $amount
     * @return float
     */
    public static function round($amount)
    {
        return number_format(
            Tools::ps_round($amount, Context::getContext()->getComputingPrecision()),
            2
        );
    }

    /**
     * Get Formatted Amount
     *
     * @param float $amount
     * @param string $currencySynbol
     * @return string
     */
    public static function getFormattedAmount($amount, $currencySynbol)
    {
        $amount = self::round($amount);
        return sprintf("%s" . $amount, $currencySynbol);
    }

    /**
     * Get Sezzle Template Path
     *
     * @return string
     */
    public static function getModuleTemplatePath()
    {
        return sprintf('@Modules/%s/views/templates/hook/', Sezzle::MODULE_NAME);
    }
}
