<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Sockbin
 * @package     Sockbin_Solidcommerce
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

/**
 * Create table 'solidcommerce'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('solidcommerce/solidcommerce'))
    ->addColumn('solidcommerce_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Solidcommerce Id')
    ->addColumn('solidcommerce_type', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Solidcommerce Type')
    ->addColumn('solidcommerce_filename', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Solidcommerce Filename')
    ->addColumn('solidcommerce_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Solidcommerce Path')
    ->addColumn('solidcommerce_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => true,
        ), 'Solidcommerce Time')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store id')
    ->addIndex($installer->getIdxName('solidcommerce/solidcommerce', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('solidcommerce/solidcommerce', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Solidcommerce');

$installer->getConnection()->createTable($table);

$installer->endSetup();
