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
$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'amazon_sku');

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
$installer->endSetup();
