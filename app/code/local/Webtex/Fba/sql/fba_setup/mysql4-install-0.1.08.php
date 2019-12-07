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

//$this->run("
//DROP TABLE IF EXISTS `{$this->getTable('mws/query')}`
//");

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('mws/query')} (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `class` varchar(50) NOT NULL DEFAULT '' COMMENT 'class name',
  `method` varchar(50) NOT NULL DEFAULT '' COMMENT 'method name',
  `request_id` varchar(50) NOT NULL DEFAULT '' COMMENT 'Amazon request id',
  `input_data` BLOB COMMENT 'query serialized input data',
  `create_date` DATETIME NOT NULL COMMENT 'query create date',
  `execution_time` decimal(10,2) unsigned COMMENT 'query timer',
  `last_execution_date` DATETIME NOT NULL COMMENT 'last execution date',
  `status` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'operation status',
  `required` int(1) unsigned NOT NULL DEFAULT 0 COMMENT 'query resend by cron until success status if this field is 1',
  `error_message` varchar (255),
  `error_xml` BLOB,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Amazon queries' AUTO_INCREMENT=0 ;
");

//$this->run("
//DROP TABLE IF EXISTS `{$this->getTable('mws/product')}`
//");

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('mws/product')} (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `sku` VARCHAR(64) NOT NULL COMMENT 'seller sku',
  `in_stock_qty` int(10) NOT NULL default 0,
  `total_qty` int(10) NOT NULL default 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Amazon products inventory info' AUTO_INCREMENT=0 ;

");

//$installer->getConnection()->dropColumn($installer->getTable('sales/shipment'), 'amazon_shipment_id');
$installer->getConnection()
    ->addColumn($installer->getTable('sales/shipment'), 'amazon_shipment_id', 'VARCHAR(255) default NULL');

//$this->run("
//DROP TABLE IF EXISTS `{$this->getTable('mws/tracking')}`
//");

$this->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('mws/tracking')}` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `carrier_code` varchar(50),
 `carrier_name` varchar(100),
 `carrier_analog` varchar(20),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
");

$installer->getConnection()->addColumn($installer->getTable('sales/shipment_track'), 'amazon_track', 'BOOLEAN default 0');

//$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'is_fba');

$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'), 'fba_query_id', 'INT(10)');

$installer->getConnection()
    ->addColumn($installer->getTable('mws/product'), 'magento_ordered_qty', 'INT(10) NOT NULL default 0');

$installer->getConnection()
    ->addColumn($installer->getTable('mws/product'), 'change_date', 'DATETIME');

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'amazon_sku', array(
    'group' => 'General',
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'backend' => '',
    'frontend' => '',
    'label' => 'Amazon SKU',
    'input' => 'text',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => true,
    'comparable' => false,
    'visible_on_front' => true,
    'visible_in_advanced_search' => true,
    'used_in_product_listing' => true,
    'unique' => true,
    'is_configurable' => '0',
));
$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'amazon_sku', 'apply_to', 'simple');

$installer->getConnection()->dropColumn($this->getTable('mws/query'), 'error_xml');
$installer->getConnection()->addColumn(
    $installer->getTable('mws/query'), 'request', "BLOB"
);
$installer->getConnection()->addColumn(
    $installer->getTable('mws/query'), 'response', "BLOB"
);
$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'amazon_sku', 'used_in_product_listing', true);
$installer->getConnection()->addColumn(
    $installer->getTable('mws/query'), 'parent_id', "int(10) NOT NULL DEFAULT 0"
);
$installer->getConnection()->addColumn(
    $installer->getTable('mws/query'), 'locked', Varien_Db_Ddl_Table::TYPE_BOOLEAN . " NOT NULL DEFAULT false"
);
$installer->getConnection()->addColumn(
    $installer->getTable('mws/query'), 'priority', "int(5) NOT NULL DEFAULT 0"
);

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

$installer->endSetup();
