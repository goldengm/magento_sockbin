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

class Webtex_Fba_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    /**
     * redirect to module configuration section
     */
    public function modSettingsAction()
    {
        Mage::app()->getResponse()->setRedirect($this->getUrl('adminhtml/system_config/edit', array('section' => 'fba')));
    }

    public function amazonLogAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('fba_tab')
            ->_title($this->__('Fulfillment by Amazon Access Log'));
        $this->renderLayout();
    }

    public function amazonQueueAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('fba_tab')
            ->_title($this->__('Fulfillment by Amazon Queue'));
        $this->renderLayout();
    }

    public function amazonQueueSendAction()
    {
        /** @var Webtex_Fba_Model_Mws_Queue $queue */
        $queue = Mage::getModel('mws/queue');
        $queue->processQueue();
        $this->_redirect('*/*/amazonQueue');
    }

    public function syncOrdersAction()
    {
        Mage::getModel('fba/observer')->refreshOrders();
        Mage::app()->getResponse()->setRedirect($this->getUrl('*/*/amazonLog'));
    }

    public function amazonQueryDetailsAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('fba_tab')
            ->_title($this->__('Fulfillment by Amazon Query Details'));
        $this->renderLayout();
    }

    public function queryGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('fba/adminhtml_queryLog_grid ')->toHtml()
        );
    }

    public function queueGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('fba/adminhtml_queue_grid ')->toHtml()
        );
    }

    public function carriersAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('fba_tab')
            ->_title($this->__('Amazon Carriers Analogs'));
        $this->renderLayout();
    }

    public function ajaxCarriersAction()
    {
        echo $this->getLayout()->createBlock('Webtex_Fba_Block_Adminhtml_Carriers', 'webstore')
            ->toHtml();
        die();
    }

    public function saveCarriersAction()
    {
        $request = $this->getRequest()->getParams();
        $changedCarrierNames = array();
        foreach (Mage::getModel('mws/tracking')->getCollection() as $carrier)
            foreach ($request['tracking'] as $track) if ($track['carrier_code'] == $carrier->getCarrierCode() && $track['carrier_analog'] != $carrier->getCarrierAnalog()) {
                $carrier->setCarrierAnalog($track['carrier_analog'])->save();
                $changedCarrierNames[$carrier->getCarrierName()] = $track['carrier_analog'];
            }

        foreach ($changedCarrierNames as $name => $analog)
            foreach (Mage::getResourceModel('sales/order_shipment_track_collection')->addFieldToFilter('title', $name)->addFieldToFilter('amazon_track', true) as $track)
                $track->setCarrierCode($analog)->save();
        echo $this->getLayout()->createBlock('Webtex_Fba_Block_Adminhtml_Carriers', 'carriers')
            ->toHtml();
        die();
    }

    public function sendOrderQueryAction()
    {
        $request = $this->getRequest()->getParams();
        if ($request['order_id']) {
            $order = Mage::getModel('sales/order')->load($request['order_id']);
            if ($order->getEntityId()) {
                $query = Mage::getModel('mws/query');
                if ($order->getFbaQueryId()) {
                    $query->load($order->getFbaQueryId());

                }
                if (!$query->getId() || $query->getStatus() == Webtex_Fba_Model_Mws_Query::STATUS_FAULT) {
                    /** @var $orderMarketplace Webtex_Fba_Model_Mws_Marketplace */
                    $orderMarketplace = Mage::getModel('mws/marketplace')->load($order->getFbaMarketplaceId());

                    if ($orderMarketplace->getId() && $orderMarketplace->getStatus()) {
                        /** @var $query Webtex_Fba_Model_Mws_Query */
                        $query = Mage::getModel('mws/query');
                        $query->setClass('outboundQ')
                            ->setMethod('createFulfillmentOrder')
                            ->setPlainData(array('order_id' => $order->getEntityId()))
                            ->setFbaMarketplaceId($orderMarketplace->getId())
                            ->postpone(1);

                        $order->setFbaQueryId($query->getId())->save();
                    }

                }
            }
            Mage::app()->getResponse()->setRedirect($this->getUrl('adminhtml/sales_order/view', array('order_id' => $request['order_id'])));
        }

    }

    public function unblockQtyAction()
    {
        $request = $this->getRequest()->getParams();
        if ($request['order_id']) {
            $order = Mage::getModel('sales/order')->load($request['order_id']);
            /** @var $orderMarketplace Webtex_Fba_Model_Mws_Marketplace */
            $orderMarketplace = Mage::getModel('mws/marketplace')->load($order->getFbaMarketplaceId());

            if ($orderMarketplace->getId() && $orderMarketplace->isAmazonInventoryMode()) {
                $blockedQty = unserialize($order->getBlockedQty());
                foreach($blockedQty as $sku => $qty) {
                    /** @var Mage_Catalog_Model_Product $product */
                    $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
                    if ($product->getId()) {
                        $assigned = Mage::getModel('mws/product')->loadByProduct($product);
                        if ($assigned->getId())
                            $assigned->incAllQty(-$qty)->save();
                    }
                }

                $order->setBlockedQty(serialize(array()));
                $order->save();
            }

            Mage::app()->getResponse()->setRedirect($this->getUrl('adminhtml/sales_order/view', array('order_id' => $request['order_id'])));
        }
    }

    public function queueMassDeleteAction()
    {
        $queryIds = (array)$this->getRequest()->getParam('query');
        if (count($queryIds)) {
            try {
                $queryCollection = Mage::getModel('mws/query')->getCollection()->addFieldToFilter('id', array('in' => $queryIds));
                foreach ($queryCollection as $query) {
                    $query->delete();
                }


                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fba')->__('The queries has been deleted.')
                );
                $this->_redirect('*/*/amazonQueue');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('fba')->__('An error occurred while deleting the queries. Please review the log and try again.')
                );
                Mage::logException($e);
                $this->_redirect('*/*/amazonQueue');
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('fba')->__('Unable to find a queries to delete.')
        );
        $this->_redirect('*/*/amazonQueue');
    }

    public function resetQueueAction(){
        /** @var Webtex_Fba_Model_Mws_Queue $queue */
        $queue = Mage::getModel('mws/queue');
        $queue->forceUnlock();
        $this->_redirect('*/*/amazonQueue');
    }

    public function inventoryMassSyncAction()
    {
        $ids = (array)$this->getRequest()->getParam('product');
        $items = array();
        /** @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
        $productCollection = Mage::getModel('catalog/product')->getCollection();
        $productCollection->addAttributeToSelect('fba_marketplace_id')->addAttributeToFilter('fba_marketplace_id', array('neq' => 0))->addFieldToFilter('entity_id', array('in' => $ids));
        foreach ($productCollection as $product) {
            /** @var $assigned Webtex_Fba_Model_Mws_Product */
            $assigned = Mage::getModel('mws/product')->loadByProduct($product);
            if ($assigned->getMarketplace()->isAmazonInventoryMode() && $assigned->getId())
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
                    ->postpone();
            }
        }

        Mage::app()->getResponse()->setRedirect($this->getUrl('adminhtml/catalog_product/index'));
    }

    public function refreshAmazonQtyAction()
    {
        $ids = (array)$this->getRequest()->getParam('product');

        /** @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
        $productCollection = Mage::getModel('catalog/product')->getCollection();
        $productCollection->addAttributeToSelect('fba_marketplace_id')->addAttributeToFilter('fba_marketplace_id', array('neq' => 0))->addFieldToFilter('entity_id', array('in' => $ids));
        foreach ($productCollection as $product)
            Mage::getModel('mws/product')->checkProductStock($product);

        Mage::app()->getResponse()->setRedirect($this->getUrl('adminhtml/catalog_product/index'));
    }

    public function syncProductBySkuAction()
    {
        $sku = $this->getRequest()->getParam('product_sku');
        $id = $this->getRequest()->getParam('product_id');
        $product = Mage::getModel('catalog/product')->load($id);
        if ($product->getIsFba())
            Mage::dispatchEvent('webtex_fba_sync_inventory', array('items' => array(Mage::getModel('mws/product')->loadByProduct($product)->getSku())));
        Mage::app()->getResponse()->setRedirect($this->getUrl('adminhtml/catalog_product/edit', array(
            'id' => $id,
            'back' => 'edit',
            'tab' => 'product_info_tabs_fba',
            'active_tab' => null
        )));
    }
}