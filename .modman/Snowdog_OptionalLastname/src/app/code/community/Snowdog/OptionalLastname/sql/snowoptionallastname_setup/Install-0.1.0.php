<?php

$installer = $this;

$installer->startSetup();
$eavTable = $this->getTable('eav_attribute');

$installer->run("UPDATE {$eavTable} SET `is_required` = 0 WHERE `attribute_code` = 'lastname';");

$installer->endSetup();