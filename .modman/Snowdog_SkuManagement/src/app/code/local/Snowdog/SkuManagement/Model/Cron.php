<?php

/**
 * Class Snowdog_SkuManagement_Model_Cron
 */
class Snowdog_SkuManagement_Model_Cron
{

    /**
     * Rebuild stock for sku management parents
     */
    public function rebuildStock()
    {
        /* @var $linkModel Mage_Catalog_Model_Product_Link */
        $linkModel = Mage::getModel('catalog/product_link');
        /* @var $productModel Mage_Catalog_Model_Product */
        $productModel = Mage::getModel('catalog/product');
        /* @var $stockModel Mage_CatalogInventory_Model_Stock_Item */
        $stockModel = Mage::getModel('cataloginventory/stock_item');
        $groupedLinks = [];

        $collection = $linkModel->getCollection()
            ->addFieldToFilter(
                'link_type_id',
                Snowdog_SkuManagement_Model_Catalog_Product_Link::LINK_TYPE_CUSTOM
            );

        /* @var $link Mage_Catalog_Model_Product_Link */
        foreach ($collection as $link) {
            $groupedLinks[$link->getProductId()][] = $link->getLinkedProductId();
        }

        foreach ($groupedLinks as $key => $groupedLink) {
            $parentInStock = 1;

            Mage::log(
                "----------------------------------------------------",
                null,
                'skumanagement_cron.log',
                true
            );

            foreach ($groupedLink as $childId) {
                /* @var $childLoaded Mage_Catalog_Model_Product */
                $childLoaded = $productModel->load($childId);
                /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
                $stockItem = $stockModel->loadByProduct($childLoaded);

                if ($stockItem->getQty() <= 0 || !$stockItem->getIsInStock()) {
                    $parentInStock = 0;

                    Mage::log(
                        "Detected child whith NO correct qty (sku: {$childLoaded->getSku()})",
                        null,
                        'skumanagement_cron.log',
                        true
                    );
                } else {
                    Mage::log(
                        "Child whith correct qty (sku: {$childLoaded->getSku()})",
                        null,
                        'skumanagement_cron.log',
                        true
                    );
                }

                $childLoaded->unsetData();
            }

            /* @var $parentLoaded Mage_Catalog_Model_Product */
            $parentLoaded = $productModel->load($key);

            if ($parentLoaded) {
                $parentStock = $stockModel->loadByProduct($parentLoaded);

                Mage::log(
                    "Setting stock for parent (sku: {$parentLoaded->getSku()}): $parentInStock",
                    null,
                    'skumanagement_cron.log',
                    true
                );

                try {
                    $parentStock->setIsInStock($parentInStock);
                    $parentStock->save();
                    $parentStock->unsetData();
                } catch (Exception $e) {
                    Mage::log(
                        "ERROR: Item stock couldn't be changed (sku: {$parentLoaded->getSku()}): stock $parentInStock. {$e->getMessage()}",
                        null,
                        'skumanagement_cron.log',
                        true
                    );
                }
            }
        }
    }

}