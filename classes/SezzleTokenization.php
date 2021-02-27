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

use Sezzle\Model\Session;
use Sezzle\Model\Tokenize;

/**
 * Class SezzleTokenization.
 */
class SezzleTokenization extends ObjectModel
{
    /** @var int Sezzle Tokenization Table ID */
    public $id_sezzle_transaction;

    /** @var int Customer ID */
    public $id_customer;

    /** @var string Sezzle Tokenization Token */
    public $token;

    /** @var string Sezzle Tokenization Customer UUID */
    public $customer_uuid;

    /** @var string Sezzle Tokenization Customer UUID Expiration */
    public $customer_uuid_expiration;

    /** @var bool Sezzle Tokenization Approved */
    public $approved;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'sezzle_tokenization',
        'primary' => 'id_sezzle_tokenization',
        'multilang' => false,
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'token' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'customer_uuid' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'customer_uuid_expiration' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'approved' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
        'collation' => 'utf8_general_ci'
    );

    /**
     * @return int
     */
    public function getIdSezzleTransaction()
    {
        return $this->id_sezzle_transaction;
    }

    /**
     * @param int $id_sezzle_transaction
     * @return SezzleTokenization
     */
    public function setIdSezzleTransaction(int $id_sezzle_transaction)
    {
        $this->id_sezzle_transaction = $id_sezzle_transaction;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdCustomer()
    {
        return $this->id_customer;
    }

    /**
     * @param int $id_customer
     * @return SezzleTokenization
     */
    public function setIdCustomer(int $id_customer)
    {
        $this->id_customer = $id_customer;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return SezzleTokenization
     */
    public function setToken(string $token)
    {
        $this->token = $token;
        return $this;
    }


    /**
     * @return string
     */
    public function getCustomerUuid()
    {
        return $this->customer_uuid;
    }

    /**
     * @param string $customer_uuid
     * @return SezzleTokenization
     */
    public function setCustomerUuid(string $customer_uuid)
    {
        $this->customer_uuid = $customer_uuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerUuidExpiration()
    {
        return $this->customer_uuid_expiration;
    }

    /**
     * @param string $customer_uuid_expiration
     * @return SezzleTokenization
     */
    public function setCustomerUuidExpiration(string $customer_uuid_expiration)
    {
        $this->customer_uuid_expiration = $customer_uuid_expiration;
        return $this;
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param bool $approved
     * @return SezzleTokenization
     */
    public function setApproved(bool $approved)
    {
        $this->approved = $approved;
        return $this;
    }

    /**
     * Store Customer Token Record
     *
     * @param int $customerId
     * @param Tokenize $tokenize
     * @throws PrestaShopException
     */
    public function storeCustomerTokenRecord($customerId, $tokenize)
    {
        $customerUuidExpiration = rtrim($tokenize->getCustomer()->getExpiration(), 'Z');
        $customerUuidExpiration = str_replace('T', ' ', $customerUuidExpiration);
        $this->setToken($tokenize->getToken())
            ->setIdCustomer($customerId)
            ->setCustomerUuid($tokenize->getCustomer()->getUuid())
            ->setCustomerUuidExpiration($customerUuidExpiration)
            ->setApproved(true)
            ->save();
    }

    /**
     * Get Tokenization Details by Customer Id
     *
     * @param int $customerId
     * @return SezzleTokenization
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getByCustomerId($customerId)
    {
        $sql = new DbQuery();
        $sql->select(self::$definition['primary'])
            ->from(self::$definition['table'])
            ->where('id_customer = ' . (int)$customerId)
            ->orderBy(self::$definition['primary'] . " DESC");
        $idSezzleTokenization = Db::getInstance()->getValue($sql);
        return new self($idSezzleTokenization);
    }

    /**
     * Delete Tokenization Record by Customer Id
     *
     * @param int $customerId
     */
    public static function deleteTokenizationRecord($customerId)
    {
        Db::getInstance()->delete(
            OrderPayment::$definition['table'],
            sprintf(
                'id_customer = %d ORDER BY %s DESC',
                (int)$customerId,
                pSQL(OrderPayment::$definition['primary'])
            ),
            1
        );
    }
}
