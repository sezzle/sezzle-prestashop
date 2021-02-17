<?php


namespace PrestaShop\Module\Sezzle\Services;

use Cart;
use Configuration;
use Currency;
use Sezzle;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\GuzzleFactory;

/**
 * Class Capture
 * @package PrestaShop\Module\Sezzle\Services
 */
class Capture
{

    /**
     * @var Cart
     */
    private $cart;

    /**
     * Capture constructor.
     * @param Cart $cart
     */
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * Capture Payment
     *
     * @param string $orderUuid
     * @param bool $isPartial
     * @return Sezzle\Model\Order\Capture
     * @throws Sezzle\HttpClient\RequestException
     */
    public function capturePayment($orderUuid, $isPartial = false)
    {
        $currency = new Currency($this->cart->id_currency);
        $captureModel = new Sezzle\Model\Order\Capture();
        $captureModel->setCaptureAmount(Util::getAmountObject($this->cart->getOrderTotal(), $currency->iso_code))
            ->setPartialCapture($isPartial);

        $apiMode = Configuration::get(Sezzle::$formFields["live_mode"])
            ? Sezzle::MODE_PRODUCTION
            : Sezzle::MODE_SANDBOX;
        $captureService = new Sezzle\Services\CaptureService(new ClientService(
            new GuzzleFactory(),
            $apiMode
        ));
        return $captureService->capturePayment(
            Authentication::getToken(),
            $orderUuid,
            $captureModel->toArray()
        );
    }
}
