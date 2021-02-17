<?php


namespace PrestaShop\Module\Sezzle\Services;

use Cart;
use Configuration;
use Currency;
use Sezzle;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\GuzzleFactory;

/**
 * Class Order
 * @package PrestaShop\Module\Sezzle\Services
 */
class Order
{

    /**
     * Update Order Reference ID
     *
     * @param string $orderUuid
     * @param string $referenceId
     * @return bool
     * @throws Sezzle\HttpClient\RequestException
     */
    public static function updateOrderReferenceId($orderUuid, $referenceId)
    {
        $apiMode = Configuration::get(Sezzle::$formFields["live_mode"])
            ? Sezzle::MODE_PRODUCTION
            : Sezzle::MODE_SANDBOX;
        $orderService = new Sezzle\Services\OrderService(new ClientService(
            new GuzzleFactory(),
            $apiMode
        ));
        return $orderService->updateOrder(
            Authentication::getToken(),
            $orderUuid,
            ['reference_id' => $referenceId]
        );
    }
}
