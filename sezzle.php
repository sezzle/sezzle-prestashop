<?php
/**
 * 2007-2022 PrestaShop
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

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\Module\Sezzle\Handler\GatewayRegion;
use PrestaShop\Module\Sezzle\Handler\Payment\Capture;
use PrestaShop\Module\Sezzle\Handler\Payment\Refund;
use PrestaShop\Module\Sezzle\Handler\Payment\Release;
use PrestaShop\Module\Sezzle\Handler\Util;
use PrestaShop\Module\Sezzle\Setup\InstallerFactory;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
use Sezzle\Config;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

require_once _PS_MODULE_DIR_ . 'sezzle/vendor/autoload.php';

class Sezzle extends PaymentModule
{
    protected $postErrors = array();
    protected $html = "";

    const MODE_SANDBOX = "sandbox";
    const MODE_PRODUCTION = "production";
    const MODULE_NAME = "sezzle";

    const ACTION_AUTHORIZE = "authorize";
    const ACTION_AUTHORIZE_CAPTURE = "authorize_capture";

    const SUPPORTED_REGIONS = ['US', 'EU'];
    const SEZZLE_GATEWAY_REGION_KEY = "SEZZLE_GATEWAY_REGION";

    public static $formFields = [
        "live_mode" => "SEZZLE_LIVE_MODE",
        "merchant_id" => "SEZZLE_MERCHANT_ID",
        "public_key" => "SEZZLE_PUBLIC_KEY",
        "private_key" => "SEZZLE_PRIVATE_KEY",
        "payment_action" => "SEZZLE_PAYMENT_ACTION",
        "tokenize" => "SEZZLE_TOKENIZE",
        "widget_enable" => "SEZZLE_WIDGET_ENABLE"
    ];
    /**
     * @var string
     */
    private $logo_url;
    /**
     * @var string
     */
    private $gatewayRegion;

    /**
     * Sezzle constructor.
     */
    public function __construct()
    {
        $this->name = 'sezzle';
        $this->tab = 'payments_gateways';
        $this->version = '2.0.0';
        $this->author = 'Sezzle';
        $this->module_key = 'de1effcde804e599e716e0eefcb6638c';
        $this->need_instance = 1;
        $this->controllers = array('complete', 'redirect');
        $this->logo_url = 'https://d34uoa9py2cgca.cloudfront.net/branding/sezzle-logos/png/sezzle-logo-sm-100w.png';

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Sezzle');
        $this->description = $this->l(
            '
                Sezzle is a public-benefit corporation on a mission to financially empower
                the next generation. Sezzle’s Buy Now, Pay Later product gives eCommerce
                shoppers more buying power by allowing them to split their payment in four,
                and pay over the course of six weeks. You get paid right away, in full,
                and Sezzle assumes all risk of fraud, chargeback and repayment.'
        );
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall Sezzle?');
        $this->limited_countries = array('US', 'CA', 'DE', 'FR', 'ES', 'IT');
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
    }

    /**
     * Install Action
     *
     * @return bool|string
     */
    public function install()
    {
        if (extension_loaded('curl') == false) {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
            return false;
        }

        // checking merchant country
        $isoCode = Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'));
        if (in_array($isoCode, $this->limited_countries) == false) {
            $this->_errors[] = $this->l('This module is not available in your country');
            return false;
        }

        if (!parent::install()) {
            return false;
        }

        foreach (static::$formFields as $field) {
            Configuration::updateValue($field, false);
        }

        // install essentials
        $installer = InstallerFactory::create($this);
        return $installer->install();
    }

    /**
     * Uninstall Action
     *
     * @return bool
     */
    public function uninstall()
    {
        $installer = InstallerFactory::create($this);
        $result = parent::uninstall() && $installer->uninstall();

        foreach (static::$formFields as $field) {
            Configuration::deleteByName($field);
        }
        Configuration::deleteByName(self::SEZZLE_GATEWAY_REGION_KEY);

        return $result;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        // If values have been submitted in the form, process.
        if ((bool)Tools::isSubmit('submitSezzleModule')) {
            $this->validateConfigForm();
            if (!count($this->postErrors)) {
                $this->postProcess();
            } else {
                foreach ($this->postErrors as $err) {
                    $this->html .= $this->displayError($err);
                }
            }
        } else {
            $this->html .= '<br />';
        }

        return $this->html . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSezzleModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live Mode'),
                        'name' => static::$formFields['live_mode'],
                        'is_bool' => true,
                        'desc' => $this->l('Use Sezzle in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => static::$formFields['merchant_id'],
                        'desc' => $this->l('Enter a valid merchant id'),
                        'label' => $this->l('Merchant Id'),
                        'required' => true,
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => static::$formFields['public_key'],
                        'desc' => $this->l('Enter a valid public key'),
                        'label' => $this->l('Public Key'),
                        'required' => true,
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => static::$formFields['private_key'],
                        'desc' => $this->l('Enter a valid private key'),
                        'label' => $this->l('Private Key'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Payment Action'),
                        'desc' => $this->l('Select a payment action'),
                        'name' => static::$formFields['payment_action'],
                        'options' => array(
                            'query' => array(
                                array(
                                    'payment_action' => self::ACTION_AUTHORIZE,
                                    'payment_action_label' => 'Authorize Only'
                                ),
                                array(
                                    'payment_action' => self::ACTION_AUTHORIZE_CAPTURE,
                                    'payment_action_label' => 'Authorize and Capture'
                                )
                            ),
                            'id' => 'payment_action',
                            'name' => 'payment_action_label'
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Allow Customer Tokenization'),
                        'name' => static::$formFields['tokenize'],
                        'is_bool' => true,
                        'desc' => $this->l('Enable tokenization option during checkout'),
                        'values' => array(
                            array(
                                'id' => 'tokenize_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'tokenize_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable Widget'),
                        'name' => static::$formFields['widget_enable'],
                        'is_bool' => true,
                        'desc' => $this->l('Show Sezzle Widget in PDP and Cart Page'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            static::$formFields['live_mode'] => Configuration::get(static::$formFields['live_mode']),
            static::$formFields['merchant_id'] => Configuration::get(static::$formFields['merchant_id']),
            static::$formFields['public_key'] => Configuration::get(static::$formFields['public_key']),
            static::$formFields['private_key'] => Configuration::get(static::$formFields['private_key']),
            static::$formFields['payment_action'] => Configuration::get(static::$formFields['payment_action']),
            static::$formFields['tokenize'] => Configuration::get(static::$formFields['tokenize']),
            static::$formFields['widget_enable'] => Configuration::get(static::$formFields['widget_enable']),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $formValues = $this->getConfigFormValues();

        foreach (array_keys($formValues) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
        Configuration::updateValue(self::SEZZLE_GATEWAY_REGION_KEY, $this->gatewayRegion);
        $this->html .= $this->displayConfirmation($this->l('Settings updated'));
    }

    /**
     * Validate Config Form before postProcess
     */
    private function validateConfigForm()
    {
        $formValues = $this->getConfigFormValues();
        foreach (array_keys($formValues) as $key) {
            if ($key === static::$formFields['live_mode']
                || $key === static::$formFields['tokenize']
                || $key === static::$formFields['widget_enable']) {
                continue;
            }

            if (!Tools::getValue($key)) {
                $readableKey = Tools::ucwords(Tools::strReplaceFirst(
                    "_",
                    " ",
                    Tools::strReplaceFirst("sezzle_", " ", Tools::strtolower($key))
                ));
                $this->postErrors[] = sprintf("Invalid %s", $readableKey);
            }
        }
        $gatewayRegion = new GatewayRegion();
        if (!$gatewayRegion = $gatewayRegion->get()) {
            $this->postErrors[] = sprintf("Invalid API Keys.");
            return;
        }
        $this->gatewayRegion = $gatewayRegion;
    }

    /**
     * Check Currency
     *
     * @param Cart $cart
     * @return bool
     */
    public function checkCurrency($cart)
    {
        $currencyOrder = new Currency($cart->id_currency);
        $currenciesModule = $this->getCurrency($cart->id_currency);
        if (is_array($currenciesModule)) {
            foreach ($currenciesModule as $currencyModule) {
                if ($currencyOrder->id == $currencyModule['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $merchantId = Configuration::get(self::$formFields['merchant_id']);
        $isWidgetEnabled = Configuration::get(self::$formFields['widget_enable']);
        if (!$isWidgetEnabled || !$merchantId) {
            return;
        }
        $gatewayRegion = Configuration::get(self::SEZZLE_GATEWAY_REGION_KEY);
        $sezzleDomain = Config::getSezzleDomain($gatewayRegion);
        $widgetURL = sprintf(
            "https://widget.%s/v1/javascript/price-widget?uuid=%s",
            $sezzleDomain,
            $merchantId
        );

        $this->context->controller->registerJavascript(
            $this->name . '-widget-config',
            '/modules/sezzle/views/js/widget-config.js',
            ['position' => 'top']
        );
        $this->context->controller->registerJavascript(
            $this->name . '-widget',
            $widgetURL,
            ['server' => 'remote']
        );
    }

    /**
     * Return payment options available for PS 1.7+
     *
     * @param array Hook parameters
     * @return PaymentOption[]|void
     */
    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }

        $publicKey = Configuration::get(static::$formFields['public_key']);
        $privateKey = Configuration::get(static::$formFields['private_key']);
        // hide sezzle if either of public and private key are missing
        if (!$publicKey || !$privateKey) {
            return;
        }

        if (!$this->checkCurrency($params['cart'])) {
            return;
        }

        $gatewayRegion = Configuration::get(self::SEZZLE_GATEWAY_REGION_KEY);
        $gatewayRegion = $gatewayRegion === 'US/CA' ? 'US' : $gatewayRegion;

        $additionalInformation = '<div id="sezzle-checkout-widget"><div id="sezzle-installment-widget-box"></div></div>
          <script>document.sezzleMerchantRegion = ' . json_encode($gatewayRegion) . ';</script>
          <script src="' . __PS_BASE_URI__ . 'modules/sezzle/views/js/installment-widget.js" type="text/javascript">
          </script>';

        $option = new PaymentOption();
        $option->setAction($this->context->link->getModuleLink($this->name, 'redirect', array(), true))
            ->setAdditionalInformation($additionalInformation)
            ->setLogo($this->logo_url);

        return [
            $option
        ];
    }

    /**
     * Release Payment
     *
     * @param array $params
     * @throws PrestaShopPaymentException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionOrderStatusPostUpdate($params)
    {
        if (!isset($params['newOrderStatus']) || !$params['newOrderStatus']
            || !isset($params['id_order']) || !$params['id_order']) {
            return;
        }

        $order = new Order($params['id_order']);
        if ($order->payment !== $this->displayName) {
            return;
        }
        if ($params['newOrderStatus']->id === (int)_PS_OS_CANCELED_) {
            try {
                $releaseHandler = new Release($order);
                $releaseHandler->execute();
            } catch (Exception $e) {
                throw new PrestaShopPaymentException("Error while releasing payment.");
            }
        }
    }

    /**
     * Partial Refund
     *
     * @param array $params
     * @throws PrestaShopPaymentException
     */
    public function hookActionOrderSlipAdd($params)
    {
        try {
            $order = null;
            // ignore if order is present in params
            if (!isset($params['order']) || !$params['order']) {
                return;
            }

            /** @var Order $order */
            $order = $params['order'];
            if ($order->payment !== $this->displayName) {
                return;
            }
            $refundHandler = new Refund($order);
            $refundHandler->execute(true);
        } catch (Exception $e) {
            if ($order instanceof Order && $order) {
                Refund::removeRefundTransaction($order->id);
            }
            throw new PrestaShopPaymentException("Error while refunding amount.");
        }
    }

    /**
     * Add Sezzle tab in Order Admin
     *
     * @param array $params
     * @return bool|string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function hookDisplayAdminOrderTabOrder($params)
    {
        return $this->render(Util::getModuleTemplatePath() . 'admin_tab_order.html.twig', [
            'sezzle' => [
                "tab_name" => $this->displayName
            ]
        ]);
    }

    /**
     * Add Sezzle tab in Order Admin
     *
     * @param array $params
     * @return bool|string
     */
    public function hookDisplayAdminOrderTabLink($params)
    {
        return $this->hookDisplayAdminOrderTabOrder($params);
    }

    /**
     * Display Sezzle Elements in Order Admin
     *
     * @param array $params
     * @return string|void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookDisplayAdminOrderContentOrder($params)
    {
        if (isset($params['id_order']) && $params['id_order']) {
            $order = new Order($params['id_order']);
        }

        if (!isset($order) && is_object($params['order'])) {
            $order = $params['order'];
        }

        if (!isset($order) || $order->payment !== $this->displayName) {
            return;
        }

        $currency = new Currency($order->id_currency);
        $txn = SezzleTransaction::getByReference($order->reference);
        $currencySymbol = method_exists($currency, 'getSymbol')
            ? $currency->getSymbol() :
            $currency->getSign();

        $templateParams = [
            'authorized_amount' => Util::getFormattedAmount($txn->getAuthorizedAmount(), $currencySymbol),
            'captured_amount' => Util::getFormattedAmount($txn->getCaptureAmount(), $currencySymbol),
            'refunded_amount' => Util::getFormattedAmount($txn->getRefundAmount(), $currencySymbol),
            'released_amount' => Util::getFormattedAmount($txn->getReleaseAmount(), $currencySymbol),
            'order_reference' => $order->reference,
            'currency_symbol' => $currencySymbol,
            'controller' => 'AdminSezzle',
            'ajax_url' => $this->context->link->getAdminLink('AdminSezzle')
        ];
        if ($txn->getAuthExpiration() > 0) {
            $dateTimeNow = new DateTime();
            $dateTimeExpire = new DateTime($txn->getAuthExpiration());
            $auth_expired = $dateTimeNow > $dateTimeExpire;
            $actual_authorized_amount = $txn->getAuthorizedAmount() - $txn->getReleaseAmount();
            $can_capture = !$auth_expired && ($actual_authorized_amount > $txn->getCaptureAmount());
            $templateParams = array_merge($templateParams, [
                'auth_expiration' => $txn->getAuthExpiration(),
                'is_auth_expired' => $auth_expired,
                'can_capture' => $can_capture
            ]);
        }
        $tokenizeTxn = SezzleTokenization::getByCustomerId($order->id_customer);
        if ($customerUUID = $tokenizeTxn->getCustomerUuid()) {
            $templateParams = array_merge($templateParams, ['customer_uuid' => $customerUUID]);
        }
        return $this->render(Util::getModuleTemplatePath() . 'admin_content_order.html.twig', [
            'sezzle' => $templateParams
        ]);
    }

    /**
     * Display Sezzle Elements in Order Admin
     *
     * @param array $params
     * @return string|void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookDisplayAdminOrderTabContent($params)
    {
        return $this->hookDisplayAdminOrderContentOrder($params);
    }

    /**
     * Render a twig template
     *
     * @param string $template
     * @param array $params
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function render($template, array $params = [])
    {
        try {
            if (!method_exists($this, 'get')) {
                throw new Exception('Method ::get() does not exist');
            }
            /** @var Twig_Environment $twig */
            $twig = $this->get('twig');
        } catch (Exception $e) {
            $twig = $this->getLegacyTwig();
        }
        return $twig->render($template, $params);
    }

    /**
     * Get a legacy twig instance with registered template paths for older PS versions (1.7.0 to 1.7.6)
     *
     * @return Twig_Environment
     */
    private function getLegacyTwig()
    {
        $kernel = new AppKernel(_PS_MODE_DEV_ ? 'dev' : 'prod', _PS_MODE_DEV_);
        $kernel->loadClassCache();
        $kernel->boot();

        /** @var Twig_Environment $twig */
        $twig = $kernel->getContainer()->get('twig');

        $loader = $twig->getLoader();
        if ($loader instanceof FilesystemLoader) {
            $loader->setPaths([$this->getLocalPath() . '../'], 'Modules');
        } elseif ($loader instanceof ChainLoader && method_exists($loader, "getLoaders")) {
            foreach ($loader->getLoaders() as $subLoader) {
                if ($subLoader instanceof FilesystemLoader) {
                    $subLoader->setPaths([$this->getLocalPath() . '../'], 'Modules');
                }
            }
        } else {
            $loader = new FilesystemLoader();
            $loader->setPaths([$this->getLocalPath() . '../'], 'Modules');
            $twig->setLoader($loader);
        }

        return $twig;
    }
}
