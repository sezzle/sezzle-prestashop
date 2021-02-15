<?php

namespace SezzleService;

use Cart;
use Configuration;
use Currency;
use Exception;
use Sezzle;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\GuzzleFactory;
use Sezzle\HttpClient\RequestException;
use Sezzle\Services\SessionService;

/**
 * Class SezzleSession
 */
class SezzleSession
{
    const REDIRECT_COMPLETE = "complete";
    const REDIRECT_CANCEL = "cancel";
    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var string
     */
    private $token;

    /**
     * SezzleSession constructor.
     * @param Cart $cart
     * @param string $token
     */
    public function __construct(Cart $cart, string $token)
    {
        $this->cart = $cart;
        $this->token = $token;
    }

    /**
     * Create Checkout Session
     *
     * @return Sezzle\Model\Session
     * @throws RequestException
     * @throws Exception
     */
    public function createSession()
    {
        $apiMode = Configuration::get(Sezzle::$formFields["live_mode"])
            ? Sezzle::MODE_PRODUCTION
            : Sezzle::MODE_SANDBOX;
        $sessionService = new SessionService(new ClientService(
            new GuzzleFactory(),
            $apiMode
        ));
        $payload = $this->buildSessionPayload();
        return $sessionService->createSession($this->token, $payload->toArray());
    }

    /**
     * Build Session Payload
     *
     * @return Sezzle\Model\Session
     * @throws Exception
     */
    private function buildSessionPayload()
    {
        // session
        $sessionModel = new Sezzle\Model\Session();

        $sessionModel->setCompleteUrl($this->getUrlObject(self::REDIRECT_COMPLETE))
            ->setCancelUrl($this->getUrlObject(self::REDIRECT_CANCEL))
            ->setOrder($this->getOrderObject())
            ->setCustomer($this->getCustomerObject());

        return $sessionModel;
    }

    /**
     * Get Redirect URL Object
     *
     * @param string $action
     * @return Sezzle\Model\Session\Url
     */
    private function getUrlObject($action)
    {
        $urlModel = new Sezzle\Model\Session\Url();
        $urlModel->setMethod(Sezzle\Config::POST);
        if ($action === self::REDIRECT_COMPLETE) {
            $href = "http://localhost/prestashop/module/sezzle/return";
            return $urlModel->setHref($href);
        }
        $href = "http://localhost/prestashop/module/sezzle/cancel";
        return $urlModel->setHref($href);
    }

    /**
     * Get Order Object
     *
     * @return Sezzle\Model\Session\Order
     * @throws Exception
     */
    private function getOrderObject()
    {
        $order = new Sezzle\Model\Session\Order();
        $order->setIntent("AUTH")
            ->setDescription("Prestashop Order")
            ->setReferenceId($this->cart->secure_key)
            ->setRequiresShippingInfo(false)
            ->setOrderAmount($this->getOrderAmountObject())
            ->setTaxAmount($this->getTaxAmountObject())
            ->setShippingAmount($this->getShippingAmountObject());
        return $order;
    }

    /**
     * Get Customer Object
     *
     * @return Sezzle\Model\Session\Customer
     */
    private function getCustomerObject()
    {
        $customer = new Sezzle\Model\Session\Customer();
        return $customer;
    }

    /**
     * Get Order Amount Object
     *
     * @return Sezzle\Model\Session\Order\Amount
     * @throws Exception
     */
    private function getOrderAmountObject()
    {
        $currency = new Currency($this->cart->id_currency);
        $amountInCents = Sezzle\Util::formatToCents($this->cart->getOrderTotal());
        return $this->getAmountObject($amountInCents, $currency->iso_code);
    }

    /**
     * Get Tax Amount Object
     *
     * @return Sezzle\Model\Session\Order\Amount
     */
    private function getTaxAmountObject()
    {
        $currency = new Currency($this->cart->id_currency);
        $amountInCents = Sezzle\Util::formatToCents($this->cart->getAverageProductsTaxRate());
        return $this->getAmountObject($amountInCents, $currency->iso_code);
    }

    /**
     * Get Shipping Amount Object
     *
     * @return Sezzle\Model\Session\Order\Amount
     */
    private function getShippingAmountObject()
    {
        $currency = new Currency($this->cart->id_currency);
        $amountInCents = Sezzle\Util::formatToCents($this->cart->getTotalShippingCost());
        return $this->getAmountObject($amountInCents, $currency->iso_code);
    }

    /**
     * Get Amount Object
     *
     * @param int $amountInCents
     * @param string $currencyCode
     * @return Sezzle\Model\Session\Order\Amount
     */
    private function getAmountObject($amountInCents, $currencyCode)
    {
        $amountModel = new Sezzle\Model\Session\Order\Amount();
        $amountModel->setAmountInCents($amountInCents)->setCurrency($currencyCode);
        return $amountModel;
    }
}
