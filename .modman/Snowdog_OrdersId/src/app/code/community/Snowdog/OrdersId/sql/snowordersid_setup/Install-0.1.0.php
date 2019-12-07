<?php

$installer = $this;

$installer->startSetup();
$eavET = $this->getTable('eav_entity_type');
$eavES = $this->getTable('eav_entity_store');

$installer->run("
UPDATE {$eavET} SET `increment_pad_length` = 4 WHERE `entity_type_code` = 'order' LIMIT 1;
UPDATE {$eavES} SET `increment_last_id` = '20000' WHERE `entity_store_id` = 1 LIMIT 1;
UPDATE {$eavES} SET `increment_prefix` = '2' WHERE `entity_store_id` = 1 LIMIT 1;
");

$installer->endSetup();