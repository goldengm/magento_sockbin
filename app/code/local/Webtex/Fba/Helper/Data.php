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

class Webtex_Fba_Helper_Data extends Mage_Core_Helper_Abstract
{
    const AWS_APPLICATION_NAME = 'Webtex_Fba';

    /**
     * get aplication name for client object
     *
     * @return string
     */
    public function getClientApplicationName()
    {
        return self::AWS_APPLICATION_NAME;
    }


    /**
     * get application version for client object
     *
     * @return string
     */
    public function getClientApplicactionVersion()
    {
        return (string)Mage::getConfig()->getNode()->modules->Webtex_Fba->version;
    }

    public function isTimeToUpdateQty($quoteId)
    {
        $lastUpdate = Mage::registry('qty_timer' . $quoteId);
        $now = $this->microtime_float();
        if (!$lastUpdate || ($now - $lastUpdate) > 1) {
            Mage::register('qty_timer' . $quoteId, intval($now));
            return true;
        }
        return false;
    }

    private function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec) / 60;
    }

    public function getProductMarketplaceId($productId)
    {
        return Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'fba_marketplace_id', Mage::app()->getDefaultStoreView()->getId());
    }

    public function getProductAmazonSku($productId)
    {
        return Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'amazon_sku', Mage::app()->getDefaultStoreView()->getId());
    }

    public function getProductWeight($productId, $storeId)
    {
        return Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'weight', $storeId);
    }

    public function isCheckFromFbaMode()
    {
        return Mage::getModel('mws/marketplace')->getCollection()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('inventory_mode', Webtex_Fba_Model_Config_Source_InventoryMode::AMAZON_MODE)->count();
    }

    public function getMultiMarketErrorMessage($storeId = null)
    {
        return Mage::getStoreConfig('fba/different_marketplaces/frontend_message', $storeId);
    }

    public function getMultiMarketMode($storeId = null)
    {
        return Mage::getStoreConfig('fba/different_marketplaces/mode', $storeId);
    }
}