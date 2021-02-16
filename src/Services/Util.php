<?php


namespace PrestaShop\Module\Sezzle\Services;

use Sezzle;

/**
 * Class Util
 * @package PrestaShop\Module\Sezzle\Services
 */
class Util
{
    /**
     * Get Amount Object
     *
     * @param int $amountInCents
     * @param string $currencyCode
     * @return Sezzle\Model\Session\Order\Amount
     */
    public static function getAmountObject($amountInCents, $currencyCode)
    {
        $amountModel = new Sezzle\Model\Session\Order\Amount();
        return $amountModel->setAmountInCents($amountInCents)->setCurrency($currencyCode);
    }
}
