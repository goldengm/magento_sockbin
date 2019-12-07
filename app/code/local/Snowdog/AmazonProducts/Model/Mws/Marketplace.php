<?php

class Snowdog_AmazonProducts_Model_Mws_Marketplace extends Webtex_Fba_Model_Mws_Marketplace
{

    /**
     * recalculates blocked qty for all marketplace orders
     *
     */
    public function recalculateBlockedQty()
    {
        if ($this->getId()
            && $this->isAmazonInventoryMode()
        ) {
            $ignoreStatuses = array(
                Mage_Sales_Model_Order::STATE_CANCELED,
                Mage_Sales_Model_Order::STATE_CLOSED,
                Mage_Sales_Model_Order::STATE_COMPLETE,
            );

            /** @var Mage_Sales_Model_Resource_Order_Collection $orderCollection */
            $orderCollectionWithQuery = Mage::getModel('sales/order')->getCollection();

            $orderCollectionWithQuery
                ->addFieldToFilter('main_table.fba_marketplace_id', $this->getId());

            $orderCollectionWithQuery->getSelect()
                /** join fba_query table to filter it by status */
                ->joinLeft(
                    array("query" => $orderCollectionWithQuery->getTable('mws/query')),
                    'main_table.fba_query_id = query.id',
                    array(
                        'query_status' => 'status'
                    )
                )
                ->where('query.status <> ' . Webtex_Fba_Model_Mws_Query::STATUS_SUCCESS)
                ->where("main_table.status not in ('" . implode("','", $ignoreStatuses) . "')");

            $orderCollection = Mage::getModel('sales/order')->getCollection();
            $orderCollection
                ->addFieldToFilter('fba_marketplace_id', $this->getId())
                ->addFieldToFilter('fba_query_id', array("null"=>true))
                ->getSelect()
                ->where("main_table.status not in ('" . implode("','", $ignoreStatuses) . "')");

            $orders = $orderCollectionWithQuery->getItems() + $orderCollection->getItems();

            $blockedQty = array();

            foreach ($orders as $order) {
                $orderBlockedQty = array();
                foreach ($order->getAllItems() as $item) {
                    /** @var Mage_Sales_Model_Order_Item $item */
                    if ($item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {

                        /******************************************/
                        /* Snowdog Amazon Products rewrite starts */
                        /******************************************/

                        $amazonProduct = Mage::helper('snowamazonproducts')->getAmazonProduct($item);

                        if(isset($amazonProduct['product'])) {
                            $product = $amazonProduct['product'];
                            $qty = $amazonProduct['qty'];
                        } else {
                            /** @var $product Mage_Catalog_Model_Product */
                            $product = $item->getProduct();
                            $qty = $item->getQtyOrdered();
                        }

                        /******************************************/
                        /* Snowdog Amazon Products rewrite ends   */
                        /******************************************/

                        $assigned = $this->_getAssigned($product);
                        if ($assigned->getFbaMarketplaceId()) {
                            $orderBlockedQty[$product->getSku()] += $qty;
                            $blockedQty[$product->getId()]['qty'] += $qty;
                            $blockedQty[$product->getId()]['product'] = $product;
                        }
                    }
                }

                $order->setBlockedQty(serialize($orderBlockedQty));
                $order->save();
            }

            $affectedIds = array();
            foreach ($blockedQty as $productId => $item) {
                if (array_key_exists($productId, $this->_assignedProducts)) {
                    $assigned = $this->_assignedProducts[$productId];
                    $assigned->setMagentoOrderedQty($item['qty'])
                        ->save();
                    $affectedIds[] = $assigned->getId();
                    /** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
                    $stockItem = Mage::getModel('catalogInventory/stock_item');
                    $stockItem->loadByProduct($item['product']);
                    if (intval($assigned->getQty()) != intval($stockItem->getQty())) {
                        $stockItem->setIsInStock(1);
                        $stockItem->setQty(floatval($assigned->getQty()))->save();
                    }
                }
            }

            if (count($affectedIds)) {
                $resource = Mage::getSingleton('core/resource');
                /** @var Varien_Db_Adapter_Interface $connection */
                $connection = $resource->getConnection('core_write');
                $connection->update(
                    $resource->getTableName('mws/product'),
                    array(
                        'magento_ordered_qty' => '0'
                    ),
                    'id NOT IN (' . implode(',', $affectedIds) . ')'
                );
            }
        }
    }

}