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
 * @category    Mage
 * @package     Sockbin_Googlebasemax
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$connection = $installer->getConnection()->dropForeignKey(
    $installer->getTable('googlebasemax/googlebasemax'),
    'FK_SITEMAP_STORE'
);


/**
 * Drop indexes
 */
$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('googlebasemax/googlebasemax'),
    'FK_SITEMAP_STORE'
);

/**
 * Change columns
 */
$tables = array(
    $installer->getTable('googlebasemax/googlebasemax') => array(
        'columns' => array(
            'googlebasemax_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Googlebasemax Id'
            ),
            'googlebasemax_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Googlebasemax Type'
            ),
            'googlebasemax_filename' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Googlebasemax Filename'
            ),
            'googlebasemax_path' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Googlebasemax Path'
            ),
            'googlebasemax_time' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Googlebasemax Time'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store id'
            )
        ),
        'comment' => 'Googlebasemax'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$connection = $installer->getConnection()->addIndex(
    $installer->getTable('googlebasemax/googlebasemax'),
    $installer->getIdxName('googlebasemax/googlebasemax', array('store_id')),
    array('store_id')
);


/**
 * Add foreign keys
 */
$connection = $installer->getConnection()->addForeignKey(
    $installer->getFkName('googlebasemax/googlebasemax', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('googlebasemax/googlebasemax'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->endSetup();
