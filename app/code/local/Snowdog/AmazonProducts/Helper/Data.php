<?php

class Snowdog_AmazonProducts_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Get amazon product from item if exists
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return array
     */
    public function getAmazonProduct($item) {
        /* @var $productModel Mage_Catalog_Model_Product */
        $productModel = Mage::getModel('catalog/product');
        $product = $productModel->loadByAttribute('sku', $item->getSku());
        $amazonProducts = $product->getAmazonProductCollection();
        $amazonProduct = array();
        
        if($amazonProducts->getSize()){
            $orderedQty = $item->getQty() ? intval($item->getQty()) : intval($item->getQtyOrdered());
            $sortedByQty = array();
            
            foreach($amazonProducts as $amProduct) {
                $amProductLoaded = $productModel->load($amProduct->getId());
                $sortedByQty[$amProductLoaded->getStockItem()->getQtyIncrements()] = $amProductLoaded;
            }

            if(isset($sortedByQty[$orderedQty])) {
                $amazonProduct['product'] = $sortedByQty[$orderedQty];
                $amazonProduct['qty'] = 1;
            } else {
                end($sortedByQty);
                $lastProduct = key($sortedByQty);
                $amazonProduct['product'] = $sortedByQty[$lastProduct];
                $amazonProduct['qty'] = floor($orderedQty / $lastProduct);
            }
        }
        
        return $amazonProduct;
    }

}
