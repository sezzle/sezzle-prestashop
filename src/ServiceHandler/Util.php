<?php


namespace PrestaShop\Module\Sezzle\ServiceHandler;

use Sezzle;

/**
 * Class Util
 * @package PrestaShop\Module\Sezzle\ServiceHandler
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
