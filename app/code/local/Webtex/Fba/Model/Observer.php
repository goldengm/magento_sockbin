<?php
/**
 * Webtex
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.webtexsoftware.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@webtexsoftware.com and we will send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.webtexsoftware.com for more information,
 * or contact us through this email: info@webtexsoftware.com.
 *
 * @category   Webtex
 * @package    Webtex_Fba
 * @copyright  Copyright (c) 2011 Webtex Solutions, LLC (http://www.webtexsoftware.com/)
 * @license    http://www.webtexsoftware.com/LICENSE.txt End-User License Agreement
 */

class Webtex_Fba_Model_Observer
{

    public function productSaveAfter($observer)
    {
        Mage::getModel('mws/product')->checkProductStock($observer->getEvent()->getProduct());
    }

    public function refresh($observer = null)
    {
        Mage::getResourceModel('mws/marketplace')->syncInventoryByLastDate();
    }


    public function orderPlaceAfter($observer)
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();

        if (!$order->getShippingCarrier()) {
           return $this;
        }

        if ($order->getShippingCarrier()->getCarrierCode() == 'fbashipping') {
            $orderBlockedQty = array();
            foreach ($order->getAllItems() as $item) {
                $product = $item->getProduct();
                /** @var $assigned Webtex_Fba_Model_Mws_Product */
                $assigned = Mage::getModel('mws/product')->loadByProduct($product);
                if ($assigned->getId() && $assigned->getMarketplace()->isAmazonInventoryMode()) {
                    if (!isset($orderBlockedQty[$item->getProduct()->getSku()])) {
                        $orderBlockedQty[$item->getProduct()->getSku()] = 0;
                    }
                    $orderBlockedQty[$item->getProduct()->getSku()] += $item->getQtyOrdered();
                    $assigned->incMagentoOrderedQty($item->getQtyOrdered())->save();
                }
                if ($assigned->getFbaMarketplaceId())
                    $order->setFbaMarketplaceId($assigned->getFbaMarketplaceId());
            }

            $order->setBlockedQty(serialize($orderBlockedQty));

            /** @var $orderMarketplace Webtex_Fba_Model_Mws_Marketplace */
            $orderMarketplace = Mage::getModel('mws/marketplace')->load($order->getFbaMarketplaceId());

            if ($orderMarketplace->getId() && $orderMarketplace->getStatus() && $orderMarketplace->sendAfterPlace()) {
                /** @var $query Webtex_Fba_Model_Mws_Query */
                $query = Mage::getModel('mws/query');
                $query->setClass('outboundQ')
                    ->setMethod('createFulfillmentOrder')
                    ->setPlainData(array('order_id' => $order->getEntityId()))
                    ->setFbaMarketplaceId($orderMarketplace->getId())
                    ->postpone(1);

                $order->setFbaQueryId($query->getId());
            }
        }
        return $this;
    }

    public function orderSaveBefore($observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();
        if ($order->getFbaMarketplaceId()
            && !$order->getFbaQueryId()
            && $order->getBaseTotalDue() == 0
        ) {
            /** @var $orderMarketplace Webtex_Fba_Model_Mws_Marketplace */
            $orderMarketplace = Mage::getModel('mws/marketplace')->load($order->getFbaMarketplaceId());
            if ($orderMarketplace->getId() && $orderMarketplace->getStatus() && $orderMarketplace->sendAfterInvoice()) {
                /** @var $query Webtex_Fba_Model_Mws_Query */
                $query = Mage::getModel('mws/query');
                $query->setClass('outboundQ')
                    ->setMethod('createFulfillmentOrder')
                    ->setPlainData(array('order_id' => $order->getEntityId()))
                    ->setFbaMarketplaceId($orderMarketplace->getId())
                    ->postpone(1);

                $order->setFbaQueryId($query->getId());
            }
        }
    }

    public function orderPlaceBefore($observer)
    {
        /** @var $order Mage_Sales_Model_Quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if (Mage::helper('fba')->isTimeToUpdateQty($quote->getEntityId())) {
            $qtyChanged = false;
            $items = array();
            foreach ($quote->getAllItems() as $item) {
                /** @var $assigned Webtex_Fba_Model_Mws_Product */
                $assigned = Mage::getModel('mws/product')->loadByProduct($item->getProduct());
                if ($assigned->getId() && $assigned->getMarketplace()->isAmazonInventoryMode() && $assigned->getMarketplace()->getCheckQtyBeforePlaceOrder())
                    $items[$assigned->getMarketplace()->getId()][] = $assigned->getSku();
            }

            foreach ($items as $marketplaceId => $skuList) {
                foreach (
                    array_chunk(
                        $skuList,
                        Webtex_Fba_Model_Mws_InventoryQ::MAX_SKU_COUNT,
                        true
                    ) as $chunk
                ) {
                    /** @var $query Webtex_Fba_Model_Mws_Query */
                    $query = Mage::getModel('mws/query');
                    $query->setClass('inventoryQ')
                        ->setMethod('syncInventoryBySkuList')
                        ->setPlainData(array('sku_array' => $chunk))
                        ->setFbaMarketplaceId($marketplaceId)
                        ->postpone(0)
                        ->execute();
                }
                $result = $query->getExecutionResult();
                if (isset($result['changed']) && $result['changed'] === true)
                    $qtyChanged = true;
            }

            if ($qtyChanged)
                Mage::getSingleton('checkout/session')->unsetAll();

        }
        return $this;
    }

    public function resendRequiredAmazonQueries()
    {
        /** @var $collection Webtex_Fba_Model_Mws_Resource_Query_Collection */
        $collection = Mage::getModel('mws/query')->getCollection();
        $collection->addFieldToFilter('required', 1)->addFieldToFilter('status', 0);
        foreach ($collection as $query)
            $query->execute();
    }

    public function refreshOrders()
    {
        Mage::getResourceModel('mws/marketplace')->syncOrdersByMarketplace();
    }

    public function addColumnsInGrid($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (get_class($block) == 'Mage_Adminhtml_Block_Catalog_Product_Grid'
            && $block->getRequest()->getControllerName() == 'catalog_product'
        ) {
            $options = Mage::getModel('mws/marketplace')->getOptionArray();

            $block->addColumnAfter('fba_marketplace_id', array(
                'header' => Mage::helper('catalog')->__('Fba Marketplace'),
                'type' => 'options',
                'width' => '70px',
                'options' => $options,
                'index' => 'fba_marketplace_id',
            ), 'status');
        } elseif ($block instanceof Mage_Adminhtml_Block_Sales_Order_Grid
            && $block->getRequest()->getControllerName() == 'sales_order'
        ) {
            $options = Mage::getModel('mws/marketplace')->getOptionArray();

            $block->addColumnAfter(
                'fba_marketplace_id',
                array(
                    'header' => Mage::helper('sales')->__('Fba Marketplace'),
                    'type' => 'options',
                    'width' => '70px',
                    'options' => $options,
                    'index' => 'fba_marketplace_id',
                    'filter_index' => 'fba_addition.fba_marketplace_id',
                ),
                'status'
            );


            $block->addColumnAfter(
                'fba_query_status',
                array(
                    'header' => Mage::helper('catalog')->__('Fba Query Status'),
                    'type' => 'options',
                    'width' => '70px',
                    'options' => Webtex_Fba_Model_Mws_Query::getStatusOptions(),
                    'index' => 'fba_query_status',
                    'filter_index' => 'fba_addition.fba_query_status',
                    'renderer' => 'fba/adminhtml_queryLog_renderer_status',
                ),
                'fba_marketplace_id'
            );

        } elseif (get_class($block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction'
            && $block->getRequest()->getControllerName() == 'catalog_product'
            && Mage::helper('fba')->isCheckFromFbaMode()
        ) {
            $block->addItem('InventoryMassSync', array(
                'label' => 'Sync Inventory with Amazon',
                'url' => Mage::app()->getStore()->getUrl('fba/adminhtml_index/inventoryMassSync'),
            ));
            $block->addItem('refreshProductsQty', array(
                'label' => 'Refresh Amazon Products Qty',
                'url' => Mage::app()->getStore()->getUrl('fba/adminhtml_index/refreshAmazonQty'),
            ));
        }
    }

    public function addAttrToProductGridFilter($observer)
    {
        $observer->getEvent()->getCollection()->addAttributeToSelect('fba_marketplace_id');
    }

    public function cartProductAddAfter($observer)
    {
        if (Mage::helper('fba')->getMultiMarketMode() == Webtex_Fba_Model_Config_Source_MultiMarketMode::RESTRICTED) {
            $last = null;
            $cartHelper = Mage::helper('checkout/cart');
            foreach ($cartHelper->getQuote()->getAllItems() as $item) {
                if ($item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
                    /** @var $assigned Webtex_Fba_Model_Mws_Product */
                    $assigned = Mage::getModel('mws/product')->loadByProduct($item->getProduct());
                    $marketplaceId = $assigned->getFbaMarketplaceId()
                    && !$assigned->shipAsNonFba()
                        ? $assigned->getFbaMarketplaceId() : 0;
                    if ($last === null)
                        $last = $marketplaceId;
                    elseif ($last != $marketplaceId)
                        Mage::throwException(Mage::helper('checkout')->__($observer->getProduct()->getName() . ':' . Mage::helper('fba')->getMultiMarketErrorMessage()));
                }
            }
        }
    }

    public function clearQueryTable()
    {
        $time = time();
        $lastMonth = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m", $time) - 1, date("d", $time), date("Y", $time)));
        /** @var $res Mage_Core_Model_Mysql4_Config_Data */
        $configData = Mage::getSingleton('core/mysql4_config_data');
        $res = Mage::getSingleton('core/resource');
        $res->getConnection('core_write')->query("DELETE FROM `{$configData->getTable('mws/query')}` WHERE `create_date`<='{$lastMonth}'");
    }

    /**
     * @param $observer
     */
    public function addExtraFieldsInOrderGrid($observer)
    {
        /** @var Mage_Sales_Model_Mysql4_Order_Grid_Collection $collection */
        $collection = $observer->getOrderGridCollection();

        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        /** @var Varien_Db_Select $subSelect */
        $subSelect = $readConnection->select();

        $subSelect->from(
            array('real_order' => $resource->getTableName('sales_flat_order')),
            array('oid' => 'entity_id', 'fba_marketplace_id')
        )->joinLeft(
                array('query' => $resource->getTableName('mws/query')),
                'real_order.fba_query_id = query.id',
                array('fba_query_status' => 'status')
            );

        $collection->getSelect()->joinLeft(
            array('fba_addition' => new Zend_Db_Expr('('.$subSelect.')')),
            'fba_addition.oid = main_table.entity_id',
            array('fba_marketplace_id','fba_query_status')
        );
    }
}