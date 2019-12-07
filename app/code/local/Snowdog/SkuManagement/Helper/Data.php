<?php

/**
 * Class Snowdog_SkuManagement_Helper_Data
 */
class Snowdog_SkuManagement_Helper_Data
    extends Mage_Core_Helper_Abstract
{

    /**
     * Get linked product from item if exists
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return array
     */
    public function getLinkedProduct($item)
    {
        /* @var $productModel Mage_Catalog_Model_Product */
        $productModel = Mage::getModel('catalog/product');
        $product = $productModel->loadByAttribute('sku', $item->getSku());
        $linkedProducts = $product->getSkuManagementProductCollection();
        $linkedProduct = array();

        if ($linkedProducts->getSize()) {
            $orderedQty = $item->getQty()
                ? intval($item->getQty())
                : intval($item->getQtyOrdered());

            $sortedByQty = array();

            foreach ($linkedProducts as $linkProduct) {
                if ($linkProduct->getData('pack_size_qty')) {
                    $sortedByQty[$linkProduct->getData('pack_size_qty')] = $linkProduct;
                }
            }

            if (isset($sortedByQty[$orderedQty])) {
                $linkedProduct['product'] = $sortedByQty[$orderedQty];
                $linkedProduct['qty'] = 1;
            } else {
                krsort($sortedByQty);

                foreach ($sortedByQty as $key => $sortedItem) {
                    if (($orderedQty % $key) == 0) {
                        $linkedProduct['product'] = $sortedByQty[$key];
                        $linkedProduct['qty'] = floor($orderedQty / $key);
                        break;
                    }
                }
            }
        }

        return $linkedProduct;
    }

}
