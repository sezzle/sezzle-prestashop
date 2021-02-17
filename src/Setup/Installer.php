<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
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
        if (!$this->registerHooks()) {
            return false;
        }

        if (!$this->installDatabase()) {
            return false;
        }

//        if (!$this->installOrderState()) {
//            return false;
//        }

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
        $orderState = new OrderState();
        $orderState->name = array();
        $orderState->module_name = $this->module->name;
        $orderState->send_email = true;
        $orderState->color = 'blue';
        $orderState->hidden = false;
        $orderState->delivery = false;
        $orderState->logable = true;
        $orderState->invoice = false;
        $orderState->paid = false;
        foreach (Language::getLanguages() as $language) {
            $orderState->template[$language['id_lang']] = 'payment';
            $orderState->name[$language['id_lang']] = 'Awaiting Sezzle Payment';
        }

        try {
            if ($orderState->add()) {
                $sezzleIcon = dirname(__FILE__) . '/logo.gif';
                $newStateIcon = dirname(__FILE__) . '/../../img/os/' . (int)$orderState->id . '.gif';
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
//        $queries = [
//            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'order_signature` (
//              `id_signature` int(11) NOT NULL AUTO_INCREMENT,
//              `id_order` int(11) NOT NULL,
//              `filename` varchar(64) NOT NULL,
//              PRIMARY KEY (`id_signature`),
//              UNIQUE KEY (`id_order`)
//            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;',
//        ];
//
//        return $this->executeQueries($queries);

        return true;
    }

    /**
     * Uninstall database modifications.
     *
     * @return bool
     */
    private function uninstallDatabase(): bool
    {
//        $queries = [
//            'DROP TABLE IF EXISTS `'._DB_PREFIX_.'order_signature`',
//        ];
//
//        return $this->executeQueries($queries);
        return true;
    }

    /**
     * Register hooks for the module.
     *
     * @return bool
     */
    private function registerHooks(): bool
    {
        // Hooks available in the order view page.
        $hooks = [
            'header',
            'backOfficeHeader',
            'paymentOptions',
            'actionPaymentConfirmation',
            'displayPayment',
            'displayPaymentReturn',
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
