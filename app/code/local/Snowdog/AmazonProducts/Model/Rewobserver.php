<?php

class Snowdog_AmazonProducts_Model_Rewobserver extends Webtex_Fba_Model_Observer {

    public function orderPlaceAfter($observer)
    {
        Mage::log("Starting order place after method", null, 'jo_snowamazon.log', true);
        /** @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();

        if (!$order->getShippingCarrier()) {
            return $this;
        }

        if ($order->getShippingCarrier()->getCarrierCode() == 'fbashipping') {
            $orderBlockedQty = array();
            foreach ($order->getAllItems() as $item) {

                /******************************************/
                /* Snowdog Amazon Products rewrite starts */
                /******************************************/

                $amazonProduct = Mage::helper('snowamazonproducts')->getAmazonProduct($item);

                if(isset($amazonProduct['product'])) {
                    // There is an Amazon Product linked
                    $product = $amazonProduct['product'];
                    $orederedQty = $amazonProduct['qty'];
                    Mage::log("Amazon product detected when place order after: " . $product->getSku(), null, 'jo_snowamazon.log', true);

                    /** @var $assigned Webtex_Fba_Model_Mws_Product */
                    $assigned = Mage::getModel('mws/product')->loadByProduct($product);
                    if ($assigned->getId() && $assigned->getMarketplace()->isAmazonInventoryMode()) {
                        if (!isset($orderBlockedQty[$product->getSku()])) {
                            $orderBlockedQty[$product->getSku()] = 0;
                        }
                        $orderBlockedQty[$product->getSku()] += $item->getQtyOrdered();
                        $assigned->incMagentoOrderedQty($orederedQty)->save();
                    }
                } else {
                    // Normal FBA process
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
                }

                /****************************************/
                /* Snowdog Amazon Products rewrite ends */
                /****************************************/


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

    public function orderPlaceBefore($observer)
    {
        Mage::log("Starting order place before method", null, 'jo_snowamazon.log', true);
        /** @var $order Mage_Sales_Model_Quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if (Mage::helper('fba')->isTimeToUpdateQty($quote->getEntityId())) {
            $qtyChanged = false;
            $items = array();
            foreach ($quote->getAllItems() as $item) {

                /******************************************/
                /* Snowdog Amazon Products rewrite starts */
                /******************************************/

                $amazonProduct = Mage::helper('snowamazonproducts')->getAmazonProduct($item);

                if(isset($amazonProduct['product'])) {
                    $realProduct = $amazonProduct['product'];
                } else {
                    $realProduct = $item->getProduct();
                }

                /******************************************/
                /* Snowdog Amazon Products rewrite ends */
                /******************************************/

                /** @var $assigned Webtex_Fba_Model_Mws_Product */
                $assigned = Mage::getModel('mws/product')->loadByProduct($realProduct);
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

}