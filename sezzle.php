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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Sezzle extends PaymentModule
{
    protected $config_form = false;
    protected $_postErrors = array();
    protected $_html = "";

    protected $_formFields = [
        "api_mode" => "SEZZLE_LIVE_MODE",
        "enable" => "SEZZLE_ENABLE",
        "public_key" => "SEZZLE_PUBLIC_KEY",
        "private_key" => "SEZZLE_PRIVATE_KEY",
        "payment_action" => "SEZZLE_PAYMENT_ACTION",
        "tokenize" => "SEZZLE_TOKENIZE"
    ];
    /**
     * @var string
     */
    private $logo_url;

    public function __construct()
    {
        $this->name = 'sezzle';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'Sezzle';
        $this->need_instance = 1;
        $this->controllers = array('payment', 'validation');
        $this->logo_url = 'https://d34uoa9py2cgca.cloudfront.net/branding/sezzle-logos/png/sezzle-logo-sm-100w.png';

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Sezzle');
        $this->description = $this->l('Sezzle is a public-benefit corporation on a mission to financially empower the next generation. Sezzle’s Buy Now, Pay Later product gives eCommerce shoppers more buying power by allowing them to split their payment in four, and pay over the course of six weeks. You get paid right away, in full, and Sezzle assumes all risk of fraud, chargeback and repayment.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall Sezzle?');

        //$this->limited_countries = array('FR');

        //$this->limited_currencies = array('EUR');

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

        //$iso_code = Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'));

//        if (in_array($iso_code, $this->limited_countries) == false)
//        {
//            $this->_errors[] = $this->l('This module is not available in your country');
//            return false;
//        }

        foreach ($this->_formFields as $field) {
            Configuration::updateValue($field, false);
        }

        //include(dirname(__FILE__) . '/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('paymentOptions') &&
            $this->registerHook('actionPaymentConfirmation') &&
            $this->registerHook('displayPayment') &&
            $this->registerHook('displayPaymentReturn');
    }

    public function uninstall()
    {
        foreach ($this->_formFields as $field) {
            Configuration::deleteByName($field);
        }

        //include(dirname(__FILE__) . '/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitSezzleModule'))) {
            $this->validateConfigForm();
            if (!count($this->_postErrors)) {
                $this->postProcess();
            } else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        } else {
            $this->_html .= '<br />';
        }

        return $this->_html . $this->renderForm();
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
                        'label' => $this->l('Enable'),
                        'name' => $this->_formFields['enable'],
                        'is_bool' => true,
                        'desc' => $this->l('Enable this module'),
                        'values' => array(
                            array(
                                'id' => 'enable_yes',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'enable_no',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => $this->_formFields['api_mode'],
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
                        'name' => $this->_formFields['public_key'],
                        'desc' => $this->l('Enter a valid public key'),
                        'label' => $this->l('Public Key'),
                        'required' => true,
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => $this->_formFields['private_key'],
                        'desc' => $this->l('Enter a valid private key'),
                        'label' => $this->l('Private Key'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Payment Action'),
                        'desc' => $this->l('Select a payment action'),
                        'name' => $this->_formFields['payment_action'],
                        'options' => array(
                            'query' => array(
                                array(
                                    'payment_action' => 'sandbox',
                                    'payment_action_label' => 'Sandbox'
                                ),
                                array(
                                    'payment_action' => 'production',
                                    'payment_action_label' => 'Production'
                                )
                            ),
                            'id' => 'payment_action',
                            'name' => 'payment_action_label'
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Allow Customer Tokenization'),
                        'name' => $this->_formFields['tokenize'],
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
            $this->_formFields['api_mode'] => Configuration::get($this->_formFields['api_mode']),
            $this->_formFields['enable'] => Configuration::get($this->_formFields['enable']),
            $this->_formFields['public_key'] => Configuration::get($this->_formFields['public_key']),
            $this->_formFields['private_key'] => Configuration::get($this->_formFields['private_key']),
            $this->_formFields['payment_action'] => Configuration::get($this->_formFields['payment_action']),
            $this->_formFields['tokenize'] => Configuration::get($this->_formFields['tokenize']),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
        $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
    }

    /**
     * Validate Config Form before postProcess
     */
    private function validateConfigForm()
    {
        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            if ($key === $this->_formFields['api_mode']
                || $key === $this->_formFields['enable']
                || $key === $this->_formFields['tokenize']) {
                continue;
            }

            if (!Tools::getValue($key)) {
                $readableKey = ucwords(str_replace("sezzle ", "", str_replace("_", " ", strtolower($key))));
                $this->_postErrors[] = sprintf("Invalid %s", $readableKey);
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
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    /**
     * Return payment options available for PS 1.7+
     *
     * @param array Hook parameters
     *
     * @return array|null
     */
    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }
        if (!$this->checkCurrency($params['cart'])) {
            return;
        }
        $option = new PaymentOption();
        $option->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true))
            ->setLogo($this->logo_url);

        return [
            $option
        ];
    }

    public function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);
        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }

    public function hookActionPaymentConfirmation()
    {
        /* Place your code here. */
    }

    public function hookDisplayPayment()
    {
        /* Place your code here. */
    }

    public function hookDisplayPaymentReturn()
    {
        /* Place your code here. */
    }
}

