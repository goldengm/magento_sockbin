<?php

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = new Mage_Sales_Model_Resource_Setup('core_setup');

/**
 * Add 'skumanagement_parent_sku' and 'skumanagement_parent_qty' attributes for entities
 */
$entities = array(
    'invoice_item',
    'creditmemo_item',
    'shipment_item'
);

$options = array(
    'type'     => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'visible'  => true,
    'required' => false
);

$options2 = array(
    'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'visible'  => true,
    'required' => false
);

foreach ($entities as $entity) {
    $installer->addAttribute($entity, 'skumanagement_parent_sku', $options);
    $installer->addAttribute($entity, 'skumanagement_parent_qty', $options2);
}

/**
 * Add 'skumanagement_parent_additional' attribute for entities
 */
$entities2 = array(
    'invoice_item',
    'creditmemo_item',
    'shipment_item',
    'order_item',
    'quote_item'
);

$options3 = array(
    'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
    'visible'  => true,
    'required' => false
);

foreach ($entities2 as $entity) {
    $installer->addAttribute($entity, 'skumanagement_parent_additional', $options3);
}

$installer->endSetup();