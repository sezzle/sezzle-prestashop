<?php

namespace PrestaShop\Module\Sezzle\ServiceHandler;

use Address;
use Cart;
use Configuration;
use Context;
use Country;
use Currency;
use Customer;
use Exception;
use PrestaShopDatabaseException;
use PrestaShopException;
use Ramsey\Uuid\Uuid;
use Sezzle;
use Sezzle\HttpClient\ClientService;
use Sezzle\HttpClient\GuzzleFactory;
use Sezzle\HttpClient\RequestException;
use Sezzle\Services\SessionService;
use State;

/**
 * Class Session
 * @package PrestaShop\Module\Sezzle\ServiceHandler
 */
class Session
{
    const REDIRECT_COMPLETE = "complete";
    const REDIRECT_CANCEL = "cancel";
    /**
     * @var Cart
     */
    private $cart;

    /**
     * SezzleSession constructor.
     * @param Cart $cart
     */
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
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
        return $sessionService->createSession(Authentication::getToken(), $payload->toArray());
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
        $link = Context::getContext()->link;
        if ($action === self::REDIRECT_COMPLETE) {
            $href = $link->getModuleLink(
                Sezzle::MODULE_NAME,
                'complete'
            );
            return $urlModel->setHref($href);
        }
        $href = $link->getPageLink(
            'order',
            null,
            null,
            'step=4'
        );
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
        return $order->setIntent("AUTH")
            ->setDescription("Prestashop Order")
            ->setReferenceId(strval($this->cart->id))
            ->setRequiresShippingInfo(false)
            ->setOrderAmount($this->getOrderAmountObject())
            ->setTaxAmount($this->getTaxAmountObject())
            ->setShippingAmount($this->getShippingAmountObject())
            ->setItems($this->getItemsObject());
    }

    /**
     * Get Items Object
     *
     * @return array
     */
    private function getItemsObject()
    {
        $products = $this->cart->getProducts(true);

        $items = [];
        foreach ($products as $key => $product) {
            $item = new Sezzle\Model\Session\Order\Item();
            if (!empty($product["reduction_applies"])
                && !empty($product["price_with_reduction"])
                && $product["reduction_applies"]) {
                $itemAmount = $product["price_with_reduction"];
            } else {
                if (!empty($product["price_wt"])) {
                    $itemAmount = $product["price_wt"];
                } else {
                    $itemAmount = $item["price"];
                }
            }
            $item->setName($product['name'])
                ->setQuantity($product['quantity'])
                ->setSku($product['reference'])
                ->setPrice($this->getItemAmountObject($itemAmount));
            $items[$key] = $item->toArray();
        }
        return $items;
    }

    /**
     * Get Customer Object
     *
     * @return Sezzle\Model\Session\Customer
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function getCustomerObject()
    {
        // customer
        $customer = new Customer((int)$this->cart->id_customer);
        // billing address
        $billingAddress = new Address($this->cart->id_address_invoice);

        $customerModel = new Sezzle\Model\Session\Customer();
        return $customerModel->setEmail($customer->email)
            ->setFirstName($customer->firstname)
            ->setLastName($customer->lastname)
            ->setPhone($billingAddress->phone)
            ->setDob($customer->birthday)
            ->setTokenize(false)
            ->setBillingAddress($this->getAddressObject("billing"))
            ->setShippingAddress($this->getAddressObject("shipping"));
    }

    /**
     * Get Address Object
     *
     * @param string $type
     * @return Sezzle\Model\Session\Customer\Address
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function getAddressObject($type)
    {
        if ($type === "billing") {
            // billing address
            $address = new Address($this->cart->id_address_invoice);
        } else {
            // shipping address
            $address = new Address($this->cart->id_address_delivery);
        }

        $country = new Country($address->id_country); // country
        $state = new State($address->id_state); // state

        $addressModel = new Sezzle\Model\Session\Customer\Address();
        return $addressModel->setName(sprintf('%s %s', $address->firstname, $address->lastname))
            ->setStreet($address->address1)
            ->setStreet2($address->address2)
            ->setState($state->iso_code)
            ->setCity($address->city)
            ->setCountryCode($country->iso_code)
            ->setPhoneNumber($address->phone)
            ->setPostalCode($address->postcode);
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
        return Util::getAmountObject($amountInCents, $currency->iso_code);
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
        return Util::getAmountObject($amountInCents, $currency->iso_code);
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
        return Util::getAmountObject($amountInCents, $currency->iso_code);
    }

    /**
     * Get Item Amount Object
     *
     * @param float $amount
     * @return Sezzle\Model\Session\Order\Amount
     */
    private function getItemAmountObject($amount)
    {
        $currency = new Currency($this->cart->id_currency);
        $amountInCents = Sezzle\Util::formatToCents($amount);
        return Util::getAmountObject($amountInCents, $currency->iso_code);
    }
}
