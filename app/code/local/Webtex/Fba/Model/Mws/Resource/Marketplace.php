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

class Webtex_Fba_Model_Mws_Resource_Marketplace extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('mws/marketplace', 'id');
    }

    public function syncInventoryByMarketplace($marketplacesIds = array())
    {
        /** @var $marketplaces Webtex_Fba_Model_Mws_Resource_Marketplace_Collection */
        $marketplaces = Mage::getModel('mws/marketplace')->getCollection();
        if (count($marketplacesIds)) $marketplaces->addFieldToFilter('id', array('in' => $marketplacesIds));
        $marketplaces->addFieldToFilter('inventory_mode', Webtex_Fba_Model_Config_Source_InventoryMode::AMAZON_MODE)->addFieldToFilter('status', 1);

        foreach ($marketplaces as $marketplace) {
            $skuList = array();
            /** @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
            $productCollection = Mage::getModel('catalog/product')->getCollection();
            $productCollection->addAttributeToSelect('fba_marketplace_id')
                ->addAttributeToFilter('fba_marketplace_id', $marketplace->getId());

            foreach ($productCollection as $product) {
                $assigned = Mage::getModel('mws/product')->loadByProduct($product);
                if ($assigned->getId())
                    $skuList[] = $assigned->getSku();
            }


            if (count($skuList)) {
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
                        ->setFbaMarketplaceId($marketplace->getId())
                        ->postpone();
                }

                $lastSyncDate = $marketplace->getLastInventorySyncDate();
                if (!$lastSyncDate)
                    $marketplace->setLastInventorySyncDate();
            }
        }
    }

    public function syncOrdersByMarketplace($marketplacesIds = array())
    {
        /** @var $marketplaces Webtex_Fba_Model_Mws_Resource_Marketplace_Collection */
        $marketplaces = Mage::getModel('mws/marketplace')->getCollection();
        if (count($marketplacesIds)) $marketplaces->addFieldToFilter('id', array('in' => $marketplacesIds));
        $marketplaces->addFieldToFilter('status', 1);

        foreach ($marketplaces as $marketplace) {
            /** @var $query Webtex_Fba_Model_Mws_Query */
            $query = Mage::getModel('mws/query');
            $query->setClass('outboundQ')
                ->setMethod('getOrderChangesByDate')
                ->setFbaMarketplaceId($marketplace->getId())
                ->postpone();
        }
    }


    public function syncInventoryByLastDate($marketplacesIds = array())
    {
        /** @var $marketplaces Webtex_Fba_Model_Mws_Resource_Marketplace_Collection */
        $marketplaces = Mage::getModel('mws/marketplace')->getCollection();
        if (count($marketplacesIds)) $marketplaces->addFieldToFilter('id', array('in' => $marketplacesIds));
        $marketplaces->addFieldToFilter('inventory_mode', Webtex_Fba_Model_Config_Source_InventoryMode::AMAZON_MODE)->addFieldToFilter('status', 1);

        foreach ($marketplaces as $marketplace) {
            /** @var $query Webtex_Fba_Model_Mws_Query */
            $query = Mage::getModel('mws/query');
            $query->setClass('inventoryQ')
                ->setMethod('syncInventoryByDate')
                ->setFbaMarketplaceId($marketplace->getId())
                ->postpone();
        }
    }


}