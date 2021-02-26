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

declare(strict_types=1);

namespace PrestaShop\Module\Sezzle\Setup;

use Configuration;
use Db;
use Language;
use Module;
use OrderState;
use PrestaShopDatabaseException;
use PrestaShopException;
use Validate;

/**
 * Class responsible for modifications needed during installation/uninstallation of the module.
 */
class Installer
{

    /**
     * @var Module
     */
    private $module;

    /**
     * Installer constructor.
     * @param Module $module
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    /**
     * Module's installation entry point.
     *
     * @return bool
     */
    public function install(): bool
    {
        // register hooks
        if (!$this->registerHooks()) {
            return false;
        }

        // install database
        if (!$this->installDatabase()) {
            return false;
        }

        // install sezzle order state
        if (!$this->installOrderState()) {
            return false;
        }

        return true;
    }

    /**
     * Module's uninstallation entry point.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        return $this->uninstallDatabase();
    }

    /**
     * Install Sezzle related Order State
     */
    private function installOrderState()
    {
        if (Configuration::get('SEZZLE_AWAITING_PAYMENT')
            && !Validate::isLoadedObject(new OrderState(Configuration::get('SEZZLE_AWAITING_PAYMENT')))) {
            return false;
        }

        // instantiate order state
        $orderState = new OrderState();
        $orderState->name = array();
        $orderState->module_name = $this->module->name;
        $orderState->send_email = true;
        $orderState->color = '#34209E';
        $orderState->hidden = false;
        $orderState->delivery = false;
        $orderState->logable = false;
        $orderState->invoice = false;
        $orderState->paid = false;
        foreach (Language::getLanguages() as $language) {
            $orderState->template[$language['id_lang']] = 'payment';
            $orderState->name[$language['id_lang']] = 'Awaiting Sezzle Payment';
        }

        try {
            // store new order state
            if ($orderState->add()) {
                $sezzleIcon = dirname(__FILE__) . '/../../logo.gif';
                $newStateIcon = dirname(__FILE__) . '/../../../../img/os/' . (int)$orderState->id . '.gif';
                copy($sezzleIcon, $newStateIcon);
            }

            Configuration::updateValue('SEZZLE_AWAITING_PAYMENT', (int)$orderState->id);
            return true;
        } catch (PrestaShopDatabaseException $e) {
            return false;
        } catch (PrestaShopException $e) {
            return false;
        }
    }

    /**
     * Install the database modifications required for this module.
     *
     * @return bool
     */
    private function installDatabase(): bool
    {
        $queries = [
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'sezzle_transaction` (
                `id_sezzle_transaction` int(11) NOT NULL AUTO_INCREMENT,
                `reference` varchar(255) NOT NULL,
                `id_cart` int(11) NOT NULL,
                `order_uuid` varchar(255) NOT NULL,
                `checkout_url` varchar(255) NOT NULL,
                `checkout_amount` decimal(20,6) NOT NULL,
                `authorized_amount` decimal(20,6) NOT NULL,
                `capture_amount` decimal(20,6) NOT NULL,
                `refund_amount` decimal(20,6) NOT NULL,
                `release_amount` decimal(20,6) NOT NULL,
                PRIMARY KEY  (`id_sezzle_transaction`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;'
        ];

        return $this->executeQueries($queries);
    }

    /**
     * Uninstall database modifications.
     *
     * @return bool
     */
    private function uninstallDatabase(): bool
    {
        $queries = [
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'sezzle_transaction`',
        ];

        return $this->executeQueries($queries);
    }

    /**
     * Register hooks for the module.
     *
     * @return bool
     */
    private function registerHooks(): bool
    {
        $hooks = [
            'header',
            'backOfficeHeader',
            'paymentOptions',
            'actionPaymentConfirmation',
            'displayPayment',
            'displayPaymentReturn',
            'actionPaymentCCAdd',
            'actionOrderStatusPostUpdate',
            'displayAdminOrder',
            'displayAdminOrderLeft',
            'displayAdminOrderRight',
            'displayAdminOrderTabOrder',
            'displayAdminOrderContentOrder'
        ];

        return (bool)$this->module->registerHook($hooks);
    }

    /**
     * A helper that executes multiple database queries.
     *
     * @param array $queries
     *
     * @return bool
     */
    private function executeQueries(array $queries): bool
    {
        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }
}
