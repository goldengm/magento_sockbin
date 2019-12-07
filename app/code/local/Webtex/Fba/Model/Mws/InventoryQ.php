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

class Webtex_Fba_Model_Mws_InventoryQ extends Webtex_Fba_Model_Mws_Abstract
{
    const MAX_SKU_COUNT = 50;

    /**
     * get inbound client object
     *
     * @return Mws_FBAInventoryServiceMWS_Client
     */
    protected function _getClient()
    {
        if ($this->_client === null) {
            $this->_client = new Mws_FBAInventoryServiceMWS_Client(
                $this->_marketplace->getAccessKeyId(),
                $this->_marketplace->getPlainSecretKey(),
                $this->_marketplace->getClientConfig('/FulfillmentInventory/2010-10-01'),
                $this->_getHelper()->getClientApplicationName(),
                $this->_getHelper()->getClientApplicactionVersion()
            );
        }
        return $this->_client;
    }

    public function syncInventoryBySkuList($data)
    {
        $result = $this->_getBlankResult();

        try {
            $this->_checkMarketplace();
            $skuArray = $data['sku_array'];
            if (is_array($skuArray) && count($skuArray)) {
                $result['code'] = 1;
                $skuList = new Mws_FBAInventoryServiceMWS_Model_SellerSkuList();
                $currentSkuList = array_slice($skuArray, 0, self::MAX_SKU_COUNT);
                $nextSkuList = array_slice($skuArray, self::MAX_SKU_COUNT);

                if (count($currentSkuList)) {
                    foreach ($currentSkuList as $sku)
                        $skuList->withmember($sku);
                    $inventoryRequest = new Mws_FBAInventoryServiceMWS_Model_ListInventorySupplyRequest();
                    $inventoryRequest->setSellerSkus($skuList);
                    $inventoryRequest->setSellerId($this->_marketplace->getMerchantId());
                    $inventoryResponse = $this->_getClient()->listInventorySupply($inventoryRequest);
                    $result['request'] .= "URL: " . $this->_getClient()->lastUrl . "\n Query:" . $this->_getClient()->lastQuery;
                    $result['response'] .= $inventoryResponse->toXML();
                    $result['request_id'] .= $inventoryResponse->getResponseMetadata()->getRequestId();
                    if ($inventoryResponse->isSetListInventorySupplyResult())
                        $processingResult = $this->_processingQuery($inventoryResponse->getListInventorySupplyResult());

                    if (isset($processingResult) && is_array($processingResult))
                        $result = array_merge($result, $processingResult);

                    if (isset($processingResult) && !empty($processingResult['next_token'])) {
                        /** @var $query Webtex_Fba_Model_Mws_Query */
                        $query = Mage::getModel('mws/query');
                        $result['child_queries'][] = $query->setClass('inventoryQ')
                            ->setMethod('syncInventoryByNextToken')
                            ->setPlainData(array('next_token' => $processingResult['next_token']));
                    }

                }

                if (count($nextSkuList)) {
                    /** @var $query Webtex_Fba_Model_Mws_Query */
                    $query = Mage::getModel('mws/query');
                    $result['child_queries'][] = $query->setClass('inventoryQ')
                        ->setMethod('syncInventoryBySkuList')
                        ->setPlainData(array('sku_array' => $nextSkuList));
                }

            }
        } catch (Mws_FBAInventoryServiceMWS_Exception $e) {
            $result['message'] = $e->getMessage();
            $result['code'] = -1;
            $result['exception'] = $e;
            $result['request_id'] = $e->getRequestId();
            $result['request'] = "URL: " . $this->_getClient()->lastUrl . "\n Query:" . $this->_getClient()->lastQuery;
            $result['response'] = $e->getXML();
        }
        return $result;
    }

    public function syncInventoryByNextToken($data = array())
    {
        $result = $this->_getBlankResult();
        try {
            $this->_checkMarketplace();
            $nextToken = $data['next_token'];
            if (isset($nexToken)) {
                $inventoryRequest = new Mws_FBAInventoryServiceMWS_Model_ListInventorySupplyByNextTokenRequest();
                $inventoryRequest->setNextToken($nextToken);
                $inventoryRequest->setSellerId($this->_marketplace->getMerchantId());
                $inventoryResponse = $this->_getClient()->listInventorySupplyByNextToken($inventoryRequest);
                $result['request'] .= "URL: " . $this->_getClient()->lastUrl . "\n Query:" . $this->_getClient()->lastQuery;
                $result['response'] .= $inventoryResponse->toXML();
                $result['request_id'] .= $inventoryResponse->getResponseMetadata()->getRequestId();
                if ($inventoryResponse->isSetListInventorySupplyByNextTokenResult())
                    $processingResult = $this->_processingQuery($inventoryResponse->getListInventorySupplyByNextTokenResult());

                if (isset($processingResult) && is_array($processingResult))
                    $result = array_merge($result, $processingResult);

                if (isset($processingResult) && !empty($processingResult['next_token'])) {
                    /** @var $query Webtex_Fba_Model_Mws_Query */
                    $query = Mage::getModel('mws/query');
                    $result['child_queries'][] = $query->setClass('inventoryQ')
                        ->setMethod('syncInventoryByNextToken')
                        ->setPlainData(array('next_token' => $processingResult['next_token']));
                }
            }
        } catch (Mws_FBAInventoryServiceMWS_Exception $e) {
            $result['message'] = $e->getMessage();
            $result['code'] = -1;
            $result['exception'] = $e;
            $result['request_id'] = $e->getRequestId();
            $result['request'] = "URL: " . $this->_getClient()->lastUrl . "\n Query:" . $this->_getClient()->lastQuery;
            $result['response'] = $e->getXML();
        }
        return $result;
    }


    public function syncInventoryByDate()
    {
        $result = $this->_getBlankResult();
        try {
            $this->_checkMarketplace();
            $lastDate = $this->_marketplace->getLastInventorySyncDate();
            if (isset($lastDate)) {
                $inventoryRequest = new Mws_FBAInventoryServiceMWS_Model_ListInventorySupplyRequest();
                $inventoryRequest->setQueryStartDateTime($lastDate);
                $inventoryRequest->setSellerId($this->_marketplace->getMerchantId());
                $inventoryResponse = $this->_getClient()->listInventorySupply($inventoryRequest);
                $result['request'] .= "URL: " . $this->_getClient()->lastUrl . "\n Query:" . $this->_getClient()->lastQuery;
                $result['response'] .= $inventoryResponse->toXML();
                $result['request_id'] .= $inventoryResponse->getResponseMetadata()->getRequestId();
                if ($inventoryResponse->isSetListInventorySupplyResult())
                    $processingResult = $this->_processingQuery($inventoryResponse->getListInventorySupplyResult());

                if (isset($processingResult) && is_array($processingResult))
                    $result = array_merge($result, $processingResult);


                if (isset($processingResult) && !empty($processingResult['next_token'])) {
                    /** @var $query Webtex_Fba_Model_Mws_Query */
                    $query = Mage::getModel('mws/query');
                    $result['child_queries'][] = $query->setClass('inventoryQ')
                        ->setMethod('syncInventoryByNextToken')
                        ->setPlainData(array('next_token' => $processingResult['next_token']));
                }
                $this->_marketplace->setLastInventorySyncDate();
            }

        } catch (Mws_FBAInventoryServiceMWS_Exception $e) {
            $result['message'] = $e->getMessage();
            $result['code'] = -1;
            $result['exception'] = $e;
            $result['request_id'] = $e->getRequestId();
            $result['request'] = "URL: " . $this->_getClient()->lastUrl . "\n Query:" . $this->_getClient()->lastQuery;
            $result['response'] = $e->getXML();
        }
        return $result;
    }


    /**
     * processing query result and update inventory if necessary
     *
     * @param $inventoryResult
     * @return array
     */
    protected function _processingQuery($inventoryResult)
    {
        try {
            $result = array();
            $result['code'] = 1;
            $qty = array();
            $assignedArray = array();
            if ($inventoryResult && $inventoryResult->isSetInventorySupplyList())
                foreach ($inventoryResult->getInventorySupplyList()->getmember() as $item) {
                    $qty[$item->getSellerSKU()]['in_stock_quantity'] = $item->getInStockSupplyQuantity();
                    $qty[$item->getSellerSKU()]['total_quantity'] = $item->getTotalSupplyQuantity();
                }
            $result['next_token'] = $inventoryResult->getNextToken();
            if (count($qty))
            {
                /** @var $amazonProductCollection Webtex_Fba_Model_Mws_Resource_Product_Collection */
                $amazonProductCollection = Mage::getModel('mws/product')->getCollection();
                $amazonProductCollection->addFieldToFilter('sku', array('in', array_keys($qty)))
                    ->addFieldToFilter('fba_marketplace_id', $this->_marketplace->getId());
                $amazonProductItems = $amazonProductCollection->getItems();

                foreach ($qty as $sku => $itemsQty) {
                    $updated = false;
                    foreach ($amazonProductItems as $key => $item) {
                        /** @var $item Webtex_Fba_Model_Mws_Product */
                        if ($item->getSku() == $sku) {
                            if ($item->getTotalQty() != $itemsQty['total_quantity'] || $item->getInStockQty() != $itemsQty['in_stock_quantity']) {
                                $item->setTotalQty($itemsQty['total_quantity'])
                                    ->setInStockQty($itemsQty['in_stock_quantity'])
                                    ->save();
                                $assignedArray[$item->getSku()] = $item;
                            }

                            $updated = true;
                            unset($amazonProductItems[$key]);
                            break;
                        }
                    }
                    if (!$updated) {
                        /** @var $newItem Webtex_Fba_Model_Mws_Product */
                        $newItem = Mage::getModel('mws/product');
                        $newItem->setSku($sku)
                            ->setTotalQty($itemsQty['total_quantity'])
                            ->setInStockQty($itemsQty['in_stock_quantity'])
                            ->setFbaMarketplaceId($this->_marketplace->getId())
                            ->save();
                        $assignedArray[$newItem->getSku()] = $newItem;
                    }
                }
            }


            if (count($assignedArray) && $this->_marketplace->isAmazonInventoryMode()) {
                /** @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
                $productCollection = Mage::getModel('catalog/product')->getCollection();
                $productCollection->addAttributeToSelect('fba_marketplace_id')
                    ->addAttributeToFilter('fba_marketplace_id', $this->_marketplace->getId())
                    ->addAttributeToFilter('sku', array('in' => array_keys($assignedArray)));

                foreach ($productCollection as $product) {
                    $rawAmazonSku = Mage::helper('fba')->getProductAmazonSku($product->getId());
                    $amazonSku = $rawAmazonSku ? $rawAmazonSku : $product->getSku();
                    $assigned = $assignedArray[$amazonSku];
                    if (isset($assigned)) {
                        $stockItem = Mage::getModel('catalogInventory/stock_item');
                        $stockItem->loadByProduct($product);
                        $currentValue = intval($assigned->getQty());
                        if (intval($stockItem->getQty()) != $currentValue) {
                            $stockItem->setIsInStock(1)->setQty($currentValue)->save();
                            $result['changed'] = true;
                        } elseif (intval($stockItem->getQty()) && !$stockItem->getIsInStock()) {
                            $stockItem->setIsInStock(1)->save();
                            $result['changed'] = true;
                        }
                        if ($rawAmazonSku)
                            unset($assignedArray[$amazonSku]);
                    }
                }

                /** @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
                $productCollection = Mage::getModel('catalog/product')->getCollection();
                $productCollection->addAttributeToSelect('fba_marketplace_id')
                    ->addAttributeToSelect('amazon_sku')
                    ->addAttributeToFilter('fba_marketplace_id', $this->_marketplace->getId())
                    ->addAttributeToFilter('amazon_sku', array('in' => array_keys($assignedArray)));


                foreach ($productCollection as $product) {
                    $amazonSku = $product->getAmazonSku();
                    $assigned = $assignedArray[$amazonSku];
                    if (isset($assigned)) {
                        $stockItem = Mage::getModel('catalogInventory/stock_item');
                        $stockItem->loadByProduct($product);
                        $currentValue = intval($assigned->getQty());
                        if (intval($stockItem->getQty()) != $currentValue) {
                            $stockItem->setIsInStock(1)->setQty($currentValue)->save();
                            $result['changed'] = true;
                        } elseif (intval($stockItem->getQty()) && !$stockItem->getIsInStock()) {
                            $stockItem->setIsInStock(1)->save();
                            $result['changed'] = true;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $result['message'] = 'Postprocessing error: ' . $e->getMessage();
            $result['code'] = -1;
            $result['exception'] = $e;
        }

        return $result;
    }

}