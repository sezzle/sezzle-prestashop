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

if (!defined('_PS_VERSION_')) {
    exit;
}

use Cart;
use Currency;
use Exception;
use PrestaShop\Module\Sezzle\Handler\Service\Util;
use PrestaShopException;
use Address;
use Configuration;
use Context;
use Country;
use Customer;
use PrestaShopDatabaseException;
use Sezzle;
use State;

/**
 * Class Session
 * @package PrestaShop\Module\Sezzle\Handler
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
     * Create Session
     *
     * @return Sezzle\Model\Session
     * @throws Sezzle\HttpClient\RequestException
     * @throws Exception
     */
    public function createSession()
    {
        $payload = $this->buildSessionPayload();
        return Service\Session::createSession($payload);
    }

    /**
     * Build Session Payload
     *
     * @return Sezzle\Model\Session
     * @throws Exception
     */
    private function buildSessionPayload()
    {
        // session model
        $sessionModel = new Sezzle\Model\Session();
        return $sessionModel->setCompleteUrl($this->getUrlObject(self::REDIRECT_COMPLETE))
            ->setCancelUrl($this->getUrlObject(self::REDIRECT_CANCEL))
            ->setOrder($this->getOrderObject())
            ->setCustomer($this->getCustomerObject());
    }

    /**
     * Get Redirect URL Object
     *
     * @param string $action
     * @return Sezzle\Model\Session\Url
     */
    private function getUrlObject($action)
    {
        // url model
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
        // order model
        $order = new Sezzle\Model\Session\Order();
        return $order->setIntent("AUTH")
            ->setDescription("Prestashop Order")
            ->setReferenceId((string)$this->cart->id)
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
            // item model
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

        // customer model
        $customerModel = new Sezzle\Model\Session\Customer();
        $isTokenizeEnabled = Configuration::get(Sezzle::$formFields['tokenize']) ? true : false;
        return $customerModel->setEmail($customer->email)
            ->setFirstName($customer->firstname)
            ->setLastName($customer->lastname)
            ->setPhone($billingAddress->phone)
            ->setDob($customer->birthday)
            ->setTokenize($isTokenizeEnabled)
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

        // address model
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
