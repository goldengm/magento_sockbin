<?php

/**
 * Class Snowdog_SkuManagement_Model_Observer
 */
class Snowdog_SkuManagement_Model_Observer
    extends Varien_Object
{

    /**
     * @param $observer
     */
    public function productPrepareSave($observer)
    {
        $event = $observer->getEvent();
        $product = $event->getProduct();
        $request = $event->getRequest();
        $links = $request->getPost('links');

        if (isset($links['skumanagement']) && !$product->getSkumanagementReadonly()) {
            $product->setSkumanagementLinkData(
                Mage::helper('adminhtml/js')
                    ->decodeGridSerializedInput($links['skumanagement'])
            );
        }
    }

    /**
     * @param $observer
     */
    public function productDuplicate($observer)
    {
        $event = $observer->getEvent();
        $currentProduct = $event->getCurrentProduct();
        $newProduct = $event->getNewProduct();
        $data = array();
        $currentProduct->getLinkInstance()->useSkumanagementLinks();
        $attributes = array();
        
        foreach ($currentProduct->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        
        foreach ($currentProduct->getSkumanagementLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        
        $newProduct->setSkumanagementLinkData($data);
    }

    /**
     * @param $observer
     */
    public function salesOrderPlaceBefore($observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getOrder();
        /* @var $helper Snowdog_SkuManagement_Helper_Data */
        $helper = Mage::helper('snowskumanagement');
        /* @var $stockModel Mage_CatalogInventory_Model_Stock_Item */
        $stockModel = Mage::getModel('cataloginventory/stock_item');
        /* @var $productModel Mage_Catalog_Model_Product */
        $productModel = Mage::getModel('catalog/product');
        $itemsTobeAdded = [];

        /* @var $item Mage_Sales_Model_Order_Item */
        foreach ($order->getAllItems() as $item) {
            /* @var $linkedProduct Mage_Catalog_Model_Product */
            $linkedProduct = $helper->getLinkedProduct($item);

            if ($item->getData('quote_parent_item_id') && !empty($linkedProduct)) {
                $item->isDeleted(true);
                continue;
            }

            if (
                !empty($linkedProduct)
                && isset($linkedProduct['product'])
                && !empty($linkedProduct['product'])
            ) {
                /* @var $linkedProductObject Mage_Catalog_Model_Product */
                $linkedProductObject = $linkedProduct['product'];
                $itemData = $item->getData();
                if ($item->getProductType() == 'configurable') {
                    $itemData['skumanagement_parent_sku'] = $item->getProduct()['sku'];
                    $productOptions = $item->getProductOptions();
                    $prodOptionsSerialized = serialize($productOptions);
                    Mage::log(print_r($prodOptionsSerialized, true), null, 'asdasdasd.log', true);
                    $itemData['skumanagement_parent_additional'] = $prodOptionsSerialized;
                } else {
                    $itemData['skumanagement_parent_sku'] = $item->getSku();
                    $itemData['skumanagement_parent_additional'] = '';
                }
                $itemData['skumanagement_parent_qty'] = $item->getQtyOrdered();
                $itemData['product_id'] = $linkedProductObject->getId();
                $itemData['product_type'] = $linkedProductObject->getTypeId();
                $itemData['name'] = $linkedProductObject->getName();
                $itemData['sku'] = $linkedProductObject->getSku();
                $itemData['qty_ordered'] = $linkedProduct['qty'];

                $result = $this->reduceChildStock(
                    $stockModel,
                    $linkedProductObject,
                    $itemData['qty_ordered']
                );

                if ($result <= 0) {
                    $parentProduct = $productModel
                        ->loadByAttribute(
                            'sku',
                            $item->getSku()
                        );

                    $parentStock = $stockModel->loadByProduct($parentProduct);
                    $parentStock->setIsInStock(0);

                    try {
                        $parentStock->save();
                        $parentStock->unsetData();
                    } catch (Exception $e) {
                        Mage::log(
                            "Item couldn't be set as not in stock (sku: {$item->getSku()}). {$e->getMessage()}",
                            null,
                            'skumanagement.log',
                            true
                        );
                    }
                }

                $itemsTobeAdded[] = $itemData;
                $item->isDeleted(true);
            }
        }

        /* @var $itemModel Mage_Sales_Model_Order_Item */
        $itemModel = Mage::getModel('sales/order_item');

        foreach ($itemsTobeAdded as $item) {
            $itemModel
                ->setStoreId($order->getStore()->getStoreId())
                ->setQuoteItemId(NULL)
                ->setQuoteParentItemId(NULL)
                ->setQtyBackordered($item['qty_backordered'])
                ->setTotalQtyOrdered($item['qty_ordered'])
                ->setQtyOrdered($item['qty_ordered'])
                ->setName($item['name'])
                ->setSku($item['sku'])
                ->setPrice($item['price'])
                ->setBasePrice($item['base_price'])
                ->setOriginalPrice($item['original_price'])
                ->setRowTotal($item['row_total'])
                ->setBaseRowTotal($item['base_row_total'])
                ->setBaseTaxAmount($item['base_tax_amount'])
                ->setData('skumanagement_parent_sku', $item['skumanagement_parent_sku'])
                ->setData('skumanagement_parent_qty', $item['skumanagement_parent_qty'])
                ->setData('skumanagement_parent_additional', $item['skumanagement_parent_additional'])
                ->setDiscountAmount($item['discount_amount'])
                ->setBaseDiscountAmount($item['base_discount_amount'])
                ->setOrder($order);

            try {
                $itemModel->save();
            } catch (Exception $e) {
                Mage::log(
                    "Item couldn't be saved (sku: {$item->getSku()}). {$e->getMessage()}",
                    null,
                    'skumanagement.log',
                    true
                );
            }

            $itemModel->unsetData();
        }
    }

    /**
     * Decrease child stock
     *
     * @param Mage_CatalogInventory_Model_Stock_Item $stockModel
     * @param Mage_Catalog_Model_Product $linkedProduct
     * @param $qty
     * @return Exception|integer
     */
    private function reduceChildStock($stockModel, $linkedProduct, $qty)
    {
        $stockItem = $stockModel->loadByProduct($linkedProduct);
        $oldQty = $stockItem->getQty();
        $newQty = $oldQty - $qty;
        $stockItem->setQty($newQty);

        try {
            $stockItem->save();
            $stockItem->unsetData();

            return $newQty;
        } catch (Exception $e) {
            Mage::log(
                "Couldn't reduce stock from item (sku: {$linkedProduct->getSku()}). {$e->getMessage()}",
                null,
                'skumanagement.log',
                true
            );
        }

        return 1;
    }

}
