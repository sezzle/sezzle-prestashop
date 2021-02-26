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

use PrestaShop\Module\Sezzle\Handler\Payment\Capture;
use PrestaShop\Module\Sezzle\Handler\Payment\Refund;
use PrestaShop\Module\Sezzle\Handler\Payment\Release;
use PrestaShop\Module\Sezzle\Setup\InstallerFactory;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\Exception\CreditSlipException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidRefundException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Payment\Exception\PaymentException;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
use Sezzle\HttpClient\RequestException;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

class Sezzle extends PaymentModule
{
    protected $postErrors = array();
    protected $html = "";

    const MODE_SANDBOX = "sandbox";
    const MODE_PRODUCTION = "production";
    const MODULE_NAME = "sezzle";
    const DISPLAY_NAME = "Sezzle";

    const ACTION_AUTHORIZE = "authorize";
    const ACTION_AUTHORIZE_CAPTURE = "authorize_capture";

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
     * Sezzle constructor.
     */
    public function __construct()
    {
        $this->name = self::MODULE_NAME;
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'Sezzle';
        $this->need_instance = 1;
        $this->controllers = array('complete', 'redirect');
        $this->logo_url = 'https://d34uoa9py2cgca.cloudfront.net/branding/sezzle-logos/png/sezzle-logo-sm-100w.png';

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        // Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l(self::DISPLAY_NAME);
        $this->description = $this->l(
            '
                Sezzle is a public-benefit corporation on a mission to financially empower
                the next generation. Sezzle’s Buy Now, Pay Later product gives eCommerce
                shoppers more buying power by allowing them to split their payment in four,
                and pay over the course of six weeks. You get paid right away, in full,
                and Sezzle assumes all risk of fraud, chargeback and repayment.'
        );
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall Sezzle?');
        $this->limited_countries = array('US', 'CA');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
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

    public function uninstall()
    {
        $installer = InstallerFactory::create($this);
        $result = parent::uninstall() && $installer->uninstall();

        foreach (static::$formFields as $field) {
            Configuration::deleteByName($field);
        }

        return $result;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        // If values have been submitted in the form, process.
        if (((bool)Tools::isSubmit('submitSezzleModule'))) {
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
                $readableKey = ucwords(str_replace("sezzle ", "", str_replace("_", " ", strtolower($key))));
                $this->postErrors[] = sprintf("Invalid %s", $readableKey);
            }
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }

//        echo "<pre>";
//        print_r($_SERVER['REQUEST_URI']);
//        die();

        if (Tools::getIsset('vieworder')) {
            echo 1234;
            die();
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
        $merchantId = Configuration::get(self::$formFields['merchant_id']);
        $isWidgetEnabled = Configuration::get(self::$formFields['widget_enable']);
        if (!$isWidgetEnabled || !$merchantId) {
            return;
        }
        $widgetURL = sprintf("https://widget.sezzle.com/v1/javascript/price-widget?uuid=%s", $merchantId);
        $this->context->controller->registerJavascript(
            $this->name . '-sezzle-widget',
            $widgetURL,
            array(
                'server' => 'remote'
            )
        );
    }

    /**
     * Return payment options available for PS 1.7+
     *
     * @param array Hook parameters
     *
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
        $option = new PaymentOption();
        $option->setAction($this->context->link->getModuleLink($this->name, 'redirect', array(), true))
            ->setLogo($this->logo_url);

        return [
            $option
        ];
    }

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

    public function hookActionPaymentConfirmation()
    {
//        PrestaShopLogger::addLog("Hello", 3, null, "Sezzle", 1);
//        if (!isset($params['id_order']) || !$orderId = $params['id_order']) {
//            throw new OrderException("Payment not found.");
//        }
//
//        //$amount = $payment->amount;
//        //$reference = $payment->order_reference;
//        $order = new Order($orderId);
////        if ($orders->count() !== 1) {
////            throw new OrderException(sprintf("Multiple orders found for : %s", $reference));
////        }
//        //foreach ($orders as $order) {
//            /** @var Order $order */
//            $txn = SezzleTransaction::getByReference($order->reference);
//            $captureHandler = new Capture($order);
//            $captureHandler->execute($txn->getOrderUUID(), 0);
//        //}
    }

    public function hookDisplayPayment()
    {
        /* Place your code here. */
    }

    public function hookDisplayPaymentReturn()
    {
        /* Place your code here. */
    }

    /**
     * Manual Capture
     *
     * @param array|null $params
     * @throws OrderException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws RequestException
     */
    public function hookActionPaymentCCAdd($params)
    {
        if (!isset($params['paymentCC']) || !$payment = $params['paymentCC']) {
            throw new PaymentException("Payment not found.");
        }

        $amount = $payment->amount;
        $reference = $payment->order_reference;
        $txn = SezzleTransaction::getByReference($reference);
        // bypass for auth and capture action
        if ($txn->getAuthorizedAmount() === $txn->getCaptureAmount()) {
            return;
        }

        $orders = Order::getByReference($reference);
        if ($orders->count() !== 1) {
            throw new PaymentException(sprintf("Multiple orders found for : %s", $reference));
        }
        foreach ($orders as $order) {
            /** @var Order $order */
            $captureHandler = new Capture($order);
            $captureHandler->execute($amount);
        }
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        if (!isset($params['newOrderStatus']) || !$params['newOrderStatus']
            || !isset($params['id_order']) || !$params['id_order']) {
            return;
        }

        $order = new Order($params['id_order']);
        switch ($params['newOrderStatus']->id) {
            case _PS_OS_CANCELED_:
                try {
                    $releaseHandler = new Release($order);
                    $releaseHandler->execute();
                } catch (PrestaShopException $e) {
                } catch (RequestException $e) {
                }
                break;
            case _PS_OS_REFUND_:
                try {
                    $refundHandler = new Refund($order);
                    $refundHandler->execute();
                } catch (PrestaShopException $e) {
                } catch (RequestException $e) {
                } catch (InvalidRefundException $e) {
                }
                break;
            default:
                break;
        }
    }

    /**
     * @hook displayAdminOrderLeft
     */
    public function hookDisplayAdminOrderLeft($param)
    {
        return '<b>hookDisplayAdminOrderLeft</b>';
    }

    /**
     * displayAdminOrderRight
     */
    public function hookDisplayAdminOrderRight($param)
    {
        return '<b>hookDisplayAdminOrderRight</b>';
    }

    /**
     * @hook displayAdminOrder
     */
    public function hookDisplayAdminOrder($param)
    {
        return '<b>hookDisplayAdminOrder</b>';
    }

    /**
     * displayAdminOrderContentOrder
     */
    public function hookDisplayAdminOrderContentOrder($param)
    {
        return '<b>hookDisplayAdminOrderContentOrder</b>';
    }

    /**
     * displayAdminOrderTabOrder
     */
    public function hookDisplayAdminOrderTabOrder($param)
    {
        return '<b>hookDisplayAdminOrderTabOrder</b>';
    }

    public function hookDisplayShoppingCartFooter($params)
    {
//        $merchantId = Configuration::get(self::$formFields['merchant_id']);
//        if (!$merchantId) {
//            return;
//        }
//        $widgetURL = sprintf("https://widget.sezzle.com/v1/javascript/price-widget?uuid=%s", $merchantId);
//        $this->context->controller->addJS($widgetURL);

//        $this->context->controller->registerJavascript(
//            $this->name . '-sezzle-widget',
//            'https://widget.sezzle.com/v1/javascript/price-widget?uuid=1',
//            array(
//                'server' => 'remote'
//            )
//        );
    }

    public function hookDisplayProductPriceBlock($params)
    {
//        $merchantId = Configuration::get(self::$formFields['merchant_id']);
//        if (!$merchantId) {
//            return;
//        }
//        $widgetURL = sprintf("https://widget.sezzle.com/v1/javascript/price-widget?uuid=%s", $merchantId);
//        $this->context->controller->addJS("https://widget.sezzle.com/v1/javascript/price-widget");
//        $this->context->controller->registerJavascript(
//            $this->name . '-sezzle-widget',
//            'https://widget.sezzle.com/v1/javascript/price-widget',
//            array(
//                'server' => 'remote'
//            )
//        );
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
//        $current_controller = Tools::getValue('controller');
//
//        if ( $current_controller == "product" )
//        {
//            $this->context->controller->addJS("https://code.jquery.com/jquery-1.12.4.js");
//            //return $this->context->smarty->fetch("module:afterpay/views/templates/front/product_modal.tpl");
//        }
    }

    /**
     * Partial Refund
     *
     * @param array $params
     * @throws CreditSlipException
     * @throws PrestaShopException
     * @throws InvalidRefundException|RequestException
     */
    public function hookActionOrderSlipAdd($params)
    {
        // ignore if order is present in params
        if (!isset($params['order']) || !$params['order']) {
            throw new CreditSlipException("Order not found.");
        }

        $order = $params['order'];
        $refundHandler = new Refund($order);
        $refundHandler->execute(true);
    }
}
