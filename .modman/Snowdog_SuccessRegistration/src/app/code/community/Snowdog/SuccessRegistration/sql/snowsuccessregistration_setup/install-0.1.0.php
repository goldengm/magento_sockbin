<?php

$installer = $this;
$installer->startSetup();

/**
 * Create table 'snowsuccessregistration/registration'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('snowsuccessregistration/registration'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true
        ),
        'Entity ID'
    )
    ->addColumn(
        'customer_email',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array(
            'nullable'  => false
        ),
        'Customer Email'
    )
    ->addColumn(
        'order_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        10,
        array(
            'nullable'  => false
        ),
        'Order ID'
    )
    ->setComment('Snowdog Success Registration');

$installer->getConnection()->createTable($table);
$installer->endSetup();