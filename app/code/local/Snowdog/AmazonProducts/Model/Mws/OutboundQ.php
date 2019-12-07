<?php


class Snowdog_AmazonProducts_Model_Mws_OutboundQ extends Webtex_Fba_Model_Mws_OutboundQ {

    /**
     * @param Mage_Sales_Model_Order $order
     * @param $shipping_speed_id
     * @return Mws_FBAOutboundServiceMWS_Model_CreateFulfillmentOrderResponse
     */
    public function createFulfillmentOrder($data)
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = !isset($data['order']) ? Mage::getModel('sales/order')->load($data['order_id']) : $data['order'];
        $result = $this->_getBlankResult();
        try {
            $this->_checkMarketplace();
            //info
            $request = new Mws_FBAOutboundServiceMWS_Model_CreateFulfillmentOrderRequest();
            $request->setSellerId($this->_marketplace->getMerchantId())
                ->setSellerFulfillmentOrderId($order->getIncrementId())
                ->setDisplayableOrderId($order->getIncrementId())
                ->setDisplayableOrderDateTime(Mage::getModel('core/date')->date('c', $order->getCreatedAtDate()->getTimestamp()))
                ->setDisplayableOrderComment($order->getIncrementId());
            //ship to address
            $line1 = $line2 = $line3 = $company = "";
            $shippingAddress = $order->getShippingAddress();
            $lines = $shippingAddress->getStreet();
            $company = $shippingAddress->getCompany();
            $line1 = trim($lines[0]);
            if (isset($lines[1]))
                $line2 = $lines[1];
            if ($company && $company != '')
                $line2 .= " Company: '" . $company . "' ";
            $line2 = trim($line2);
            if (isset($lines[2]))
                $line3 .= $lines[2];
            if (isset($lines[3]))
                $line3 .= " " . $lines[3];
            $line3 = trim($line3);
            $destinationAddress = new Mws_FBAOutboundServiceMWS_Model_Address(array(
                    'Name' => $shippingAddress->getName(),
                    'City' => $shippingAddress->getCity(),
                    'StateOrProvinceCode' => $shippingAddress->getRegionCode(),
                    'CountryCode' => $shippingAddress->getCountryModel()->getIso2Code(),
                    'PostalCode' => $shippingAddress->getPostcode(),
                    'PhoneNumber' => $shippingAddress->getTelephone(),
                )
            );

            if (strlen($line1) > 0)
                $destinationAddress->setLine1($line1);
            if (strlen($line2) > 0)
                $destinationAddress->setLine2($line2);
            if (strlen($line3) > 0)
                $destinationAddress->setLine3($line3);

            $request->setDestinationAddress($destinationAddress);
            //shipping speed category
            $method = ucfirst(substr($order->getShippingMethod(), 15));
            $request->setShippingSpeedCategory($method);
            //items
            $requestItems = new Mws_FBAOutboundServiceMWS_Model_CreateFulfillmentOrderItemList();
            foreach ($order->getAllItems() as $item) {
                /** @var $product Mage_Catalog_Model_Product */
                /** @var $item Mage_Sales_Model_Order_Item */

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

                if (!$product) $product = Mage::getModel('catalog/product')->load($item->getProductId());
                /** @var $assigned Webtex_Fba_Model_Mws_Product */
                $assigned = Mage::getModel('mws/product')->loadByProduct($product);
                if ($assigned->getId()
                    && $assigned->getMarketplace()->getId() == $this->_marketplace->getId()
                ) {

                    $requestItems->withmember(
                        new Mws_FBAOutboundServiceMWS_Model_CreateFulfillmentOrderItem(
                            array(
                                'SellerSKU' => $assigned->getSku(),
                                'Quantity' => (int)$qty,
                                'SellerFulfillmentOrderItemId' => $order->getIncrementId() . '_' . $item->getItemId(),
                            )
                        )
                    );
                }
            }


            if ($requestItems->isSetmember()) {
                $request->setItems($requestItems);
                //emails
                $notifyEmails = explode(',', $this->_marketplace->getNotificationEmails());
                if ($this->_marketplace->getNotifyCustomers())
                    $notifyEmails[] = $order->getBillingAddress()->getEmail();
                $emailList = new Mws_FBAOutboundServiceMWS_Model_NotificationEmailList();
                $emailList->withmember(implode(',', $notifyEmails));
                $request->setNotificationEmailList($emailList);
                $createOrderResult = $this->_falseGetClient()->createFulfillmentOrder($request);
                $result['response'] = $createOrderResult->toXML();
                $result['request'] = "URL: " . $this->_falseGetClient()->lastUrl . "\n Query:" . $this->_falseGetClient()->lastQuery;
                if ($createOrderResult->isSetResponseMetadata()) {
                    $result['request_id'] = $createOrderResult->getResponseMetadata()->getRequestId();
                    $result['code'] = 1;
                    $lastSyncDate = $this->_marketplace->getLastOrderSyncDate();
                    if (!$lastSyncDate)
                        $this->_marketplace->setLastOrderSyncDate();

                    if ($this->_marketplace->isAmazonInventoryMode()) {
                        foreach ($order->getAllItems() as $item) {
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
                            $assigned = Mage::getModel('mws/product')->loadByProduct($product);
                            if ($assigned->getId())
                                $assigned->incAllQty(-$qty)->save();
                        }
                    }
                }
            }
        } catch (Mws_FBAOutboundServiceMWS_Exception $e) {
            $result['message'] = $e->getMessage();
            $result['code'] = -1;
            $result['exception'] = $e;
            $result['request_id'] = $e->getRequestId();
            $result['request'] = "URL: " . $this->_falseGetClient()->lastUrl . "\n Query:" . $this->_falseGetClient()->lastQuery;
            $result['response'] = $e->getXML();
        }
        return $result;
    }

    public function syncOrderProcessing(Mws_FBAOutboundServiceMWS_Model_GetFulfillmentOrderResult $orderDetailsResult)
    {
        try {
            $result = array();
            $result['code'] = 1;
            $items = $orderDetailsResult->getFulfillmentOrderItem();
            $shipments = $orderDetailsResult->getFulfillmentShipment();
            $fulfillmentOrder = $orderDetailsResult->getFulfillmentOrder();

            /** @var $order Mage_Sales_Model_Order */
            $order = Mage::getModel('sales/order')
                ->getCollection()
                ->addAttributeToFilter('increment_id', $fulfillmentOrder->getSellerFulfillmentOrderId())
                ->addAttributeToFilter('fba_marketplace_id', $this->_marketplace->getId())
                ->getFirstItem();

            if (!$order->getEntityId())
                $order = Mage::getModel('sales/order')->load($fulfillmentOrder->getSellerFulfillmentOrderId());

            if ($order->getEntityId() && $order->canShip()) {
                $orderId = $order->getEntityId();
                $orderItems = $order->getAllItems();
                if (isset($shipments)
                    && $shipments instanceof Mws_FBAOutboundServiceMWS_Model_FulfillmentShipmentList
                    && count($shipments->getmember())
                ) {
                    $shipmentsList = $shipments;
                    /** @var $shipmentsList Mws_FBAOutboundServiceMWS_Model_FulfillmentShipmentList */
                    foreach ($shipmentsList->getmember() as $amazonShipment) {
                        if (strtolower($amazonShipment->getFulfillmentShipmentStatus()) == 'shipped') {
                            $shipment = Mage::getResourceModel('sales/order_shipment_collection')
                                ->addFieldToFilter('amazon_shipment_id', $amazonShipment->getAmazonShipmentId())
                                ->addFieldToFilter('order_id', $orderId)
                                ->getFirstItem();

                            $amazonShipmentItems = $amazonShipment->getFulfillmentShipmentItem();
                            $amazonShipmentPackage = $amazonShipment->getFulfillmentShipmentPackage();

                            if (!$shipment->getEntityId()) {
                                /** @var $convertOrder Mage_Sales_Model_Convert_Order */
                                $convertOrder = Mage::getModel('sales/convert_order');
                                $shipment = $convertOrder->toShipment($order);


                                foreach ($orderItems as $orderItem) {
                                    /******************************************/
                                    /* Snowdog Amazon Products rewrite starts */
                                    /******************************************/

                                    $amazonProduct = Mage::helper('snowamazonproducts')->getAmazonProduct($orderItem);

                                    if(isset($amazonProduct['product'])) {
                                        $product = $amazonProduct['product'];
                                        $qty = $amazonProduct['qty'];
                                    } else {
                                        /** @var $product Mage_Catalog_Model_Product */
                                        $product = $orderItem->getProduct();
                                        $qty = $orderItem->getQtyOrdered();
                                    }

                                    /******************************************/
                                    /* Snowdog Amazon Products rewrite ends   */
                                    /******************************************/
                                    /** @var $assigned Webtex_Fba_Model_Mws_Product */
                                    $assigned = Mage::getModel('mws/product')->loadByProduct($product);
                                    if ($assigned->getId()
                                        && $assigned->getMarketplace()->getId() == $this->_marketplace->getId()

                                        && $product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE
                                    ) {
                                        $amazonShippedQty = 0;
                                        if (isset($amazonShipmentItems) && $amazonShipmentItems instanceof Mws_FBAOutboundServiceMWS_Model_FulfillmentShipmentItemList
                                            && count($amazonShipmentItems->getmember())
                                        ) {
                                            foreach ($amazonShipmentItems->getmember() as $item) {
                                                if ($item->getSellerFulfillmentOrderItemId() == $order->getIncrementId() . "_" . $orderItem->getItemId()
                                                    && $item->getSellerSKU() == $assigned->getSku()
                                                ) {
                                                    $amazonShippedQty += $item->getQuantity();
                                                }
                                            }
                                        }

                                        $parent = $orderItem->getParentItem();
                                        if ($amazonShippedQty) {
                                            if ($orderItem->getQtyToShip() >= $amazonShippedQty) {
                                                $itemToShip = $orderItem;
                                            } else if ($parent
                                                && $parent->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
                                                && $parent->getQtyToShip() >= $amazonShippedQty
                                            ) {
                                                $itemToShip = $parent;
                                            }

                                            if ($itemToShip) {
                                                $_eachItem = $convertOrder->itemToShipmentItem($itemToShip);
                                                $_eachItem->setQty($amazonShippedQty);
                                                $shipment->addItem($_eachItem);
                                            }
                                        }
                                    }
                                }
                                if (count($shipment->getAllItems())) {
                                    $shipment->setAmazonShipmentId($amazonShipment->getAmazonShipmentId());
                                    $shipment->register();
                                }
                            }

                            //tracking
                            $arrTracking = array();
                            if (isset($amazonShipmentPackage) && $amazonShipmentPackage instanceof Mws_FBAOutboundServiceMWS_Model_FulfillmentShipmentPackageList
                                && count($amazonShipmentPackage->getmember())
                            )
                                foreach ($amazonShipmentPackage->getmember() as $package)
                                    if ($package->isSetCarrierCode() && $package->isSetTrackingNumber())
                                        $arrTracking[] = array(
                                            'carrier_code' => $package->getCarrierCode(),
                                            'title' => $package->getCarrierCode(),
                                            'number' => $package->getTrackingNumber(),
                                            'amazon_track' => true,
                                        );
                            if (count($arrTracking))
                                $shipment = $this->addTracking($shipment, $arrTracking);
                            $shipment->getOrder()->setIsInProcess(true);
                            $saveTransaction = Mage::getModel('core/resource_transaction')
                                ->addObject($shipment)
                                ->addObject($shipment->getOrder())
                                ->save();

                            if (!$shipment->getEmailSent())
                                $shipment->sendEmail()->setEmailSent(true)->save();
                        }
                    }
                }
            }

        } catch (Exception $e) {
            $result['message'] = 'syncOrderProcessing error: ' . $e->getMessage();
            $result['code'] = -1;
            $result['exception'] = $e;
        }

        return $result;
    }

    /**
     * get inbound client object
     *
     * @return Mws_FBAOutboundServiceMWS_Client
     */
    private function _falseGetClient()
    {
        if ($this->_client === null) {
            $this->_client = new Mws_FBAOutboundServiceMWS_Client(
                $this->_marketplace->getAccessKeyId(),
                $this->_marketplace->getPlainSecretKey(),
                $this->_marketplace->getClientConfig('/FulfillmentOutboundShipment/2010-10-01'),
                $this->_getHelper()->getClientApplicationName(),
                $this->_getHelper()->getClientApplicactionVersion()
            );
        }
        return $this->_client;
    }

}