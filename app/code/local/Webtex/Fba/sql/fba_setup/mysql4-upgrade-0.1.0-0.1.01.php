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

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('fba_product')};
DROP TABLE IF EXISTS {$this->getTable('fba_assignedproducts')};
");


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
//DROP TABLE IF EXISTS `{$this->getTable('mws/info')}`
//");

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('mws/info')} (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `inventory_sync_last_date` DATETIME COMMENT 'last time when products inventory was synced',
  `orders_sync_last_date` DATETIME COMMENT 'last time when orders was synced',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Amazon info' AUTO_INCREMENT=0 ;
  INSERT INTO {$this->getTable('mws/info')} (`inventory_sync_last_date`, `orders_sync_last_date`) VALUES (null,null);
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
CREATE TABLE `{$this->getTable('mws/tracking')}` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `carrier_code` varchar(50),
 `carrier_name` varchar(100),
 `carrier_analog` varchar(20),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
");

$installer->getConnection()->addColumn($installer->getTable('sales/shipment_track'), 'amazon_track', 'BOOLEAN default 0');

$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'is_fba');

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'is_fba', array(
    'group' => 'General',
    'type' => 'int',
    'backend' => '',
    'frontend' => '',
    'label' => 'Fulfillment by Amazon',
    'input' => 'boolean',
    'source' => 'eav/entity_attribute_source_boolean',
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
    'unique' => false,
));

//$installer->getConnection()->dropColumn($installer->getTable('sales/order'), 'is_fba');
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'), 'is_fba', 'BOOLEAN NOT NULL default 0');
//$installer->getConnection()->dropColumn($installer->getTable('sales/order'), 'fba_query_id');
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'), 'fba_query_id', 'INT(10)');
//$installer->getConnection()->dropColumn($installer->getTable('mws/product'), 'magento_ordered_qty');
$installer->getConnection()
    ->addColumn($installer->getTable('mws/product'), 'magento_ordered_qty', 'INT(10) NOT NULL default 0');
$installer->endSetup();
