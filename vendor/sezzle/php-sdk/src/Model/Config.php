<?php

namespace Sezzle\Model;

class Config
{
     /**
     * @var bool
     */
    private $sezzleEnabled;

    /**
     * @var string
     */
    private $merchantUuid;

    /**
     * @var float
     */
    private $minCheckoutAmount;

    /**
     * @var bool
     */
    private $pdpWidgetEnabled;

    /**
     * @var bool
     */
    private $cartWidgetEnabled;

    /**
     * @var bool
     */
    private $installmentWidgetEnabled;

    /**
     * @var bool
     */
    private $inContextCheckoutEnabled;

    /**
     * @var string
     */
    private $inContextCheckoutMode;

    /**
     * @var string
     */
    private $paymentAction;

    /**
     * @var bool
     */
    private $tokenizationEnabled;

    /**
     * @var string
     */
    private $storeUrl;

    /**
     * @return bool
     */
    public function isSezzleEnabled()
    {
        return $this->sezzleEnabled;
    }

    /**
     * @param bool $sezzleEnabled
     */
    public function setSezzleEnabled($sezzleEnabled)
    {
        $this->sezzleEnabled = $sezzleEnabled;
    }

    /**
     * @return string
     */
    public function getMerchantUuid()
    {
        return $this->merchantUuid;
    }

    /**
     * @param string $merchantUuid
     */
    public function setMerchantUuid($merchantUuid)
    {
        $this->merchantUuid = $merchantUuid;
    }

    /**
     * @return float
     */
    public function getMinCheckoutAmount()
    {
        return $this->minCheckoutAmount;
    }

    /**
     * @param float $minCheckoutAmount
     */
    public function setMinCheckoutAmount($minCheckoutAmount)
    {
        $this->minCheckoutAmount = $minCheckoutAmount;
    }

    /**
     * @return bool
     */
    public function isPDPWidgetEnabled()
    {
        return $this->pdpWidgetEnabled;
    }

    /**
     * @param bool $pdpWidgetEnabled
     */
    public function setPDPWidgetEnabled($pdpWidgetEnabled)
    {
        $this->pdpWidgetEnabled = $pdpWidgetEnabled;
    }

    /**
     * @return bool
     */
    public function isCartWidgetEnabled()
    {
        return $this->cartWidgetEnabled;
    }

    /**
     * @param bool $cartWidgetEnabled
     */
    public function setCartWidgetEnabled($cartWidgetEnabled)
    {
        $this->cartWidgetEnabled = $cartWidgetEnabled;
    }

    /**
     * @return bool
     */
    public function isInstallmentWidgetEnabled()
    {
        return $this->installmentWidgetEnabled;
    }

    /**
     * @param bool $installmentWidgetEnabled
     */
    public function setInstallmentWidgetEnabled($installmentWidgetEnabled)
    {
        $this->installmentWidgetEnabled = $installmentWidgetEnabled;
    }

    /**
     * @return bool
     */
    public function isInContextCheckoutEnabled()
    {
        return $this->inContextCheckoutEnabled;
    }

    /**
     * @param bool $inContextCheckoutEnabled
     */
    public function setInContextCheckoutEnabled($inContextCheckoutEnabled)
    {
        $this->inContextCheckoutEnabled = $inContextCheckoutEnabled;
    }

    /**
     * @return string
     */
    public function getInContextCheckoutMode()
    {
        return $this->inContextCheckoutMode;
    }

    /**
     * @param string $inContextCheckoutMode
     */
    public function setInContextCheckoutMode($inContextCheckoutMode)
    {
        $this->inContextCheckoutMode = $inContextCheckoutMode;
    }

    /**
     * @return string
     */
    public function getPaymentAction()
    {
        return $this->paymentAction;
    }

    /**
     * @param string $paymentAction
     */
    public function setPaymentAction($paymentAction)
    {
        $this->paymentAction = $paymentAction;
    }

    /**
     * @return bool
     */
    public function isTokenizationEnabled()
    {
        return $this->tokenizationEnabled;
    }

    /**
     * @param bool $isTokenizationEnabled
     */
    public function setTokenizationEnabled($isTokenizationEnabled)
    {
        $this->isTokenizationEnabled = $isTokenizationEnabled;
    }

    /**
     * @return string
     */
    public function getStoreUrl()
    {
        return $this->storeUrl;
    }

    /**
     * @param string $storeUrl
     */
    public function setStoreUrl($storeUrl)
    {
        $this->storeUrl = $storeUrl;
    }

    /**
     * @param array $data
     * @return Config
     */
    public static function fromArray(array $data)
    {
        $config = new self();

        $config->setSezzleEnabled($data['sezzle_enabled']);
        $config->setMerchantUuid($data['merchant_uuid']);
        $config->setMinCheckoutAmount($data['min_checkout_amount']);
        $config->setPDPWidgetEnabled($data['pdp_widget_enabled']);
        $config->setCartWidgetEnabled($data['cart_widget_enabled']);
        $config->setInstallmentWidgetEnabled($data['installment_widget_enabled']);
        $config->setInContextCheckoutEnabled($data['in_context_checkout_enabled']);
        $config->setInContextCheckoutMode($data['in_context_checkout_mode']);
        $config->setPaymentAction($data['payment_action']);
        $config->setTokenizationEnabled($data['tokenization_enabled']);
        $config->setStoreUrl($data['store_url']);

        return $config;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'public_key' => $this->isSezzleEnabled(),
            'merchant_uuid' => $this->getMerchantUuid(),
            'min_checkout_amount' => $this->getMinCheckoutAmount(),
            'pdp_widget_enabled' => $this->isPDPWidgetEnabled(),
            'cart_widget_enabled' => $this->isCartWidgetEnabled(),
            'installment_widget_enabled' => $this->isInstallmentWidgetEnabled(),
            'in_context_checkout_enabled' => $this->isInContextCheckoutEnabled(),
            'in_context_checkout_mode' => $this->getInContextCheckoutMode(),
            'payment_action' => $this->getPaymentAction(),
            'tokenization_enabled' => $this->isTokenizationEnabled(),
            'store_url' => $this->getStoreUrl()
        ];
    }
}
