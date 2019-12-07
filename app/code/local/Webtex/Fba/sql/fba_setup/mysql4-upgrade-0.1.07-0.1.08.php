<?php
/**
 * Webtex
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.webtexsoftware.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@webtexsoftware.com and we will send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.webtexsoftware.com for more information,
 * or contact us through this email: info@webtexsoftware.com.
 *
 * @category   Webtex
 * @package    Webtex_Fba
 * @copyright  Copyright (c) 2011 Webtex Solutions, LLC (http://www.webtexsoftware.com/)
 * @license    http://www.webtexsoftware.com/LICENSE.txt End-User License Agreement
 */

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('mws/product'), 'fba_marketplace_id', "int(10) NOT NULL DEFAULT 0"
);

$installer->getConnection()->addColumn(
    $installer->getTable('mws/query'), 'fba_marketplace_id', "int(10) NOT NULL DEFAULT 0"
);

$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'fba_marketplace_id');
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'fba_marketplace_id', array(
    'group' => 'General',
    'type' => 'int',
    'backend' => '',
    'frontend' => '',
    'label' => 'Fulfillment by Amazon Marketplace Identifier',
    'input' => 'select',
    'source' => 'mws/marketplace',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => 0,
    'searchable' => false,
    'filterable' => true,
    'comparable' => false,
    'visible_on_front' => true,
    'visible_in_advanced_search' => true,
    'used_in_product_listing' => true,
    'unique' => false,
    'apply_to' => 'simple',
    'is_configurable' => '0'
));

$installer->getConnection()->dropTable($installer->getTable('mws/marketplace'));
$marketplaceTable = $installer->getConnection()
    ->newTable($installer->getTable('mws/marketplace'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'ID')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => false,
        'default' => false,
    ), 'Marketplace Status')
    ->addColumn('access_key_id', Varien_Db_Ddl_Table::TYPE_TEXT, 40, array(
        'nullable' => false,
    ), 'Acess Key')
    ->addColumn('secret_key', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Secret Key')
    ->addColumn('merchant_id', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(
        'nullable' => false,
    ), 'Merchant ID')
    ->addColumn('amazon_marketplace', Varien_Db_Ddl_Table::TYPE_SMALLINT, 2, array(
        'nullable' => false,
    ), 'Amazon Marketplace')
    ->addColumn('notification_emails', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
        'default' => '',
    ), 'Notification Emails')
    ->addColumn('notify_customers', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => false,
        'default' => false,
    ), 'Notify customers?')
    ->addColumn('carrier_title', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Carrier Title')
    ->addColumn('send_order_immediately', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => false,
        'default' => false,
    ), 'Send Order Immediately?')
    ->addColumn('last_queue_execution_time', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12, 2), array(), 'Last Q Execution Time')
    ->addColumn('next_queue_start_time', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(), 'Next Q Start Time')
    ->addColumn('inventory_mode', Varien_Db_Ddl_Table::TYPE_SMALLINT, 2, array(
        'nullable' => false,
        'default' => 0,
    ), 'Inventory Mode')
    ->addColumn('check_qty_before_place_order', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => false,
        'default' => false,
    ), 'check_qty_before_place_order')
    ->addColumn('qty_check_field', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'qty_check_field')
    ->addColumn('inventory_check_frequency', Varien_Db_Ddl_Table::TYPE_SMALLINT, 2, array(
        'nullable' => false,
        'default' => 0,
    ), 'inventory_check_frequency')
    ->addColumn('check_orders', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => false,
        'default' => 0,
    ), 'check_orders?')
    ->addColumn('shipping_currency', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
        'nullable' => false,
    ), 'Shipping Currency')
    ->addColumn('inventory_sync_last_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array())
    ->addColumn('orders_sync_last_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array())
    ->setComment('MWS Amazon Marketplace Table');
$installer->getConnection()->createTable($marketplaceTable);


$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'fba_marketplace_id');
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'fba_marketplace_id', array(
    'group' => 'General',
    'type' => 'int',
    'backend' => '',
    'frontend' => '',
    'label' => 'Fulfillment by Amazon Marketplace Identifier',
    'input' => 'select',
    'source' => 'mws/marketplace',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => 0,
    'searchable' => false,
    'filterable' => true,
    'comparable' => false,
    'visible_on_front' => true,
    'visible_in_advanced_search' => true,
    'used_in_product_listing' => true,
    'unique' => false,
    'apply_to' => 'simple',
    'is_configurable' => '0'
));

$installer->getConnection()->dropTable($installer->getTable('mws/shipping'));
$shippingRatesTable = $installer->getConnection()
    ->newTable($installer->getTable('mws/shipping'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'ID')
    ->addColumn('fba_marketplace_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'default' => 0,
    ), 'Marketplace Foreign Key')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => false,
        'default' => 0,
    ), 'is_method active')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Method title')
    ->addColumn('rules', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'serialized rules')
    ->addColumn('country', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'specific country codes coma-separated list')
    ->addColumn('allow_specific_country', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => false,
        'default' => 0,))
    ->addColumn('is_default', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => false,
        'default' => 0,
    ), 'is method default for fba carrier')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Shipping Method Type')
    ->setComment('MWS Amazon Marketplace Shipping Rates Table');
$installer->getConnection()->createTable($shippingRatesTable);

$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'amazon_sku', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL);

$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'), 'fba_marketplace_id', 'int(10) NOT NULL default 0');

$installer->getConnection()->dropTable($installer->getTable('fba_mws_info'));
$mwsSettingsArray = array();
$configMwsSettingsValues = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', array('like' => 'fba/%'))->getItems();
foreach ($configMwsSettingsValues as $value) {
    $path = explode('/', $value->getPath());
    $mwsSettingsArray[$value->getScope()][$value->getScopeId()][$path[2]] = $value->getValue();
}

$configMwsSettingsValues = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', array('like' => 'fbacarriers/%'))->getItems();
foreach ($configMwsSettingsValues as $value) {
    $path = explode('/', $value->getPath());
    $mwsSettingsArray[$value->getScope()][$value->getScopeId()][$path[1] . '/' . $path[2]] = $value->getValue();
}

$arrayToInsert = array();
$arrayToInsert[] = $mwsSettingsArray['default'][0];

foreach ($mwsSettingsArray as $scope)
    foreach ($scope as $scopeItem) {
        $newArray = array_merge($mwsSettingsArray['default'][0], $scopeItem);
        if ($newArray != $mwsSettingsArray['default'][0])
            $arrayToInsert[] = $newArray;
    }

$currency = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', 'currency/options/base')
    ->addFieldToFilter('scope', 'default')
    ->addFieldToFilter('scope_id', '0')
    ->getFirstItem()->getValue();

$firstMarketplace = null;

foreach ($arrayToInsert as $item) {
    /** @var $itemModel Webtex_Fba_Model_Mws_Marketplace */
    $itemModel = Mage::getModel('mws/marketplace');
    $itemModel->setAccessKeyId($item['access_key_id'])
        ->setSecretKey($item['secret_access_key'])
        ->setAmazonMarketplace($item['amazon_marketplace'])
        ->setCarrierTitle($item['carrier_title'])
        ->setCheckOrders(true)
        ->setCheckQtyBeforePlaceOrder((bool)$item['checkbeforeorderplace'])
        ->setInventoryCheckFrequency($item['checkfrequency'])
        ->setInventoryMode($item['qtycheck'])
        ->setMerchantId($item['seller_id'])
        ->setNextQueueStartTime()
        ->setNotificationEmails($item['notify_emails'])
        ->setNotifyCustomers((bool)$item['user_email_notify'])
        ->setQtyCheckField($item['checkfield'])
        ->setSendOrderImmediately($item['send_immediately'])
        ->setShippingCurrency($currency)
        ->setStatus((bool)$item['mws_status'])
        ->save();

    $shippingTypes = Mage::getModel('fba/config_source_shippingType')->toOptionArray();
    $shippingData = array();

    foreach ($item as $key => $node) {
        $shippingType = null;
        $keyExploded = explode('/', $key);
        if (isset($keyExploded[0]) && isset($keyExploded[1])) {
            foreach ($shippingTypes as $type) {
                if (stristr($keyExploded[0], $type['label']) !== FALSE) {
                    $shippingData[$type['label']][$keyExploded[1]] = $node;
                    $shippingData[$type['label']]['type'] = $type['value'];
                }
            }

        }
    }


    foreach ($shippingData as $shipping) {
        /** @var $shippingModel Webtex_Fba_Model_Mws_Shipping */
        $shippingModel = Mage::getModel('mws/shipping');
        $shippingModel->setCountry($shipping['specificcountry'])
            ->setAllowSpecificCountry($shipping['sallowspecific'])
            ->setFbaMarketplaceId($itemModel->getId())
            ->setIsActive($shipping['isactive'])
            ->setIsDefault($shipping['default'])
            ->setRules(unserialize($shipping['rules']))
            ->setTitle($shipping['title'])
            ->setType($shipping['type'])
            ->save();

    }

    if ($firstMarketplace == null)
        $firstMarketplace = $itemModel;
}
$installer->run("INSERT INTO `{$this->getTable('catalog/product')}_int` (`entity_type_id`,`attribute_id`,`entity_id`,`value`)
        SELECT `eType`.`entity_type_id`, `attr2`.`attribute_id`,`ent`.`entity_id`,0
        FROM `{$this->getTable('eav/entity_type')}` AS `eType`
        JOIN `{$this->getTable('eav/attribute')}` AS `attr2` ON `attr2`.`entity_type_id` = `eType`.`entity_type_id`
        JOIN `{$this->getTable('catalog/product')}` as `ent`
        WHERE `eType`.`entity_type_code` =  'catalog_product'
                AND `attr2`.`attribute_code` =  'fba_marketplace_id'
                AND `type_id` = 'simple'
    ON DUPLICATE KEY UPDATE
        value=0;");

$mainTable = $this->getTable('eav/attribute');
$attribute = $this->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'is_fba');
if ($attribute) {


    $installer->run("INSERT INTO `{$this->getTable('catalog/product')}_int` (`entity_type_id`,`attribute_id`,`entity_id`,`value`)
        SELECT `eType`.`entity_type_id`, `attr2`.`attribute_id`,`ent`.`entity_id`,{$firstMarketplace->getId()}
        FROM `{$this->getTable('eav/entity_type')}` AS `eType`
        JOIN `{$this->getTable('eav/attribute')}` AS `attr1` ON `attr1`.`entity_type_id` = `eType`.`entity_type_id`
        JOIN `{$this->getTable('eav/attribute')}` AS `attr2` ON `attr2`.`entity_type_id` = `eType`.`entity_type_id`
        JOIN `{$this->getTable('catalog/product')}_int` as `ent` ON `attr1`.`attribute_id` = `ent`.`attribute_id`
        WHERE `eType`.`entity_type_code` =  'catalog_product'
        AND `attr1`.`attribute_code` =  'is_fba'
            AND `ent`.`value` = 1
                AND `attr2`.`attribute_code` =  'fba_marketplace_id'
    ON DUPLICATE KEY UPDATE
        value={$firstMarketplace->getId()};");


    $installer->run("UPDATE {$this->getTable('sales/order')} SET `fba_marketplace_id`={$firstMarketplace->getId()} WHERE `is_fba`=1 ");
    $installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'is_fba');
}

$installer->getConnection()
    ->dropColumn($installer->getTable('sales/order'), 'is_fba');
$installer->endSetup();
