<?php
 
$installer = $this;
 
$installer->startSetup();

$installer->getConnection()->modifyColumn($installer->getTable('wishlist/wishlist'), "customer_id", "INT( 10 ) UNSIGNED NULL DEFAULT NULL COMMENT 'Customer ID'");
 
$installer->endSetup();