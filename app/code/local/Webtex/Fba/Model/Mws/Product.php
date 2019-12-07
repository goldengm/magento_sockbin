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

/**
 * amazon query model
 * Table: 'fba_mws_product'
 * Fields:
 *  - id - primary key
 *  - sku - seller sku
 *  - in_stock_qty - qty received by Amazon
 *  - total_qty - qty shipped to Amazon
 *  - change_date - datetime field with last change date
 *  - fba_marketplace_id - amazon marketplace id for multistore support
 *
 * methods:
 * @method int getId()
 * @method Webtex_Fba_Model_Mws_Product setClass(string)
 * @method string getSku()
 * @method Webtex_Fba_Model_Mws_Product setSku(string)
 * @method string getMethod()
 * @method Webtex_Fba_Model_Mws_Product setInStockQty(int)
 * @method int getInStockQty()
 * @method Webtex_Fba_Model_Mws_Product setTotalQty(int)
 * @method int getTotalQty()
 * @method Webtex_Fba_Model_Mws_Product setChangeDate(string)
 * @method string getChangeDate()
 * @method Webtex_Fba_Model_Mws_Product setMagentoOrderedQty(int)
 * @method int getMagentoOrderedQty()
 * @method Webtex_Fba_Model_Mws_Product setFbaMarketplaceId(int)
 * @method int getFbaMarketplaceId()
 * @method Webtex_Fba_Model_Mws_Resource_Product getResource()
 * @method float getExecutionTime()
 */
class Webtex_Fba_Model_Mws_Product extends Mage_Core_Model_Abstract
{

    /** @var \Webtex_Fba_Model_Mws_Marketplace|null */
    protected $_marketplace = null;

    public function _construct()
    {
        parent::_construct();
        $this->_init('mws/product');
    }

    /**
     * load Amazon info by magento product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Varien_Object
     */
    public function loadByProduct(Mage_Catalog_Model_Product $product)
    {
        $amazonSku = Mage::helper('fba')->getProductAmazonSku($product->getId());
        $marketplaceId = Mage::helper('fba')->getProductMarketplaceId($product->getId());
        /** @var $marketplace Webtex_Fba_Model_Mws_Marketplace */
        $marketplace = Mage::getModel('mws/marketplace')->load($marketplaceId);
        if ($marketplace->getId() && $marketplace->getStatus()) {
            $this->setData($this->getResource()->loadByProduct($product, $amazonSku, $marketplaceId));

            if ($marketplaceId && (!$this->getId() || (trim($amazonSku) != "" && $amazonSku != $product->getSku()))) {
                if (trim($amazonSku) != "")
                    $this->setSku($amazonSku);
                else
                    $this->setSku($product->getSku());
                $this->setFbaMarketplaceId($marketplaceId);
                $this->save();
            }
        }

        return $this;
    }

    /**
     * @return Webtex_Fba_Model_Mws_Marketplace
     */
    public function getMarketplace()
    {
        if ($this->_marketplace === null && $this->getFbaMarketplaceId())
            $this->_marketplace = Mage::getModel('mws/marketplace')->load($this->getFbaMarketplaceId());

        return $this->_marketplace;
    }

    public function getQty()
    {
        return $this->getData($this->getQtyField()) - $this->getMagentoOrderedQty();
    }

    public function setQty($value)
    {
        return $this->setData($this->getQtyField(), $value);
    }

    public function incMagentoOrderedQty($value)
    {
        return $this->setMagentoOrderedQty($this->getMagentoOrderedQty() + $value);
    }

    public function incAllQty($value)
    {
        return $this->setTotalQty($this->getTotalQty() + $value)
            ->setInStockQty($this->getInStockQty() + $value)
            ->incMagentoOrderedQty($value);
    }


    protected function _beforeSave()
    {
        if ($this->hasDataChanges() && $this->getData() != $this->getOrigData() || !$this->getChangeDate())
            $this->setChangeDate(date("Y-m-d H:i:s", time()));
        return parent::_beforeSave();
    }

    private function getQtyField()
    {
        return $this->getMarketplace()->getQtyCheckField();
    }

    public function checkProductStock(Mage_Catalog_Model_Product $product)
    {
        $marketplaceId = Mage::helper('fba')->getProductMarketplaceId($product->getId());
        if (Mage::getModel('mws/marketplace')->load($marketplaceId)->isAmazonInventoryMode()) {
            /** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
            $stockItem = Mage::getModel('catalogInventory/stock_item');
            $stockItem->loadByProduct($product);
            $this->loadByProduct($product);
            if (intval($this->getQty()) != intval($stockItem->getQty())) {
                $stockItem->setIsInStock(1);
                $stockItem->setQty(floatval($this->getQty()))->save();
            }

        }

    }

    public function updateProduct()
    {
        if ($this->getSku() && $this->getFbaMarketplaceId()) {
            $productCollection = Mage::getModel('catalog/product')->getCollection();
            $productCollection->addAttributeToSelect('fba_marketplace_id')
                ->addAttributeToFilter('fba_marketplace_id', $this->getFbaMarketplaceId())
                ->addAttributeToFilter('sku', array('eq' => $this->getSku()));

            foreach ($productCollection as $product) {
                $marketplaceId = Mage::helper('fba')->getProductMarketplaceId($product->getId());
                $rawAmazonSku = Mage::helper('fba')->getProductAmazonSku($product->getId());
                if (Mage::getModel('mws/marketplace')->load($marketplaceId)->isAmazonInventoryMode() && (!$rawAmazonSku || $rawAmazonSku == $product->getSku())) {
                    /** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
                    $stockItem = Mage::getModel('catalogInventory/stock_item');
                    $stockItem->loadByProduct($product);
                    if (intval($this->getQty()) != intval($stockItem->getQty())) {
                        $stockItem->setIsInStock(1);
                        $stockItem->setQty(floatval($this->getQty()))->save();
                    }

                }
            }

            $productCollection = Mage::getModel('catalog/product')->getCollection();
            $productCollection->addAttributeToSelect('fba_marketplace_id')
                ->addAttributeToSelect('amazon_sku')
                ->addAttributeToFilter('fba_marketplace_id', $this->getFbaMarketplaceId())
                ->addAttributeToFilter('amazon_sku', array('eq' => $this->getSku()));

            foreach ($productCollection as $product) {
                $marketplaceId = Mage::helper('fba')->getProductMarketplaceId($product->getId());
                if (Mage::getModel('mws/marketplace')->load($marketplaceId)->isAmazonInventoryMode()) {
                    /** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
                    $stockItem = Mage::getModel('catalogInventory/stock_item');
                    $stockItem->loadByProduct($product);
                    if (intval($this->getQty()) != intval($stockItem->getQty())) {
                        $stockItem->setIsInStock(1);
                        $stockItem->setQty(floatval($this->getQty()))->save();
                    }

                }
            }
        }

    }

    public function shipAsNonFba()
    {
        return $this->getMarketplace()->getInventoryMode() == Webtex_Fba_Model_Config_Source_InventoryMode::AMAZON_MODE
            && $this->getMarketplace()->getShipOosAsNonFba()
            && $this->getQty() == 0;
    }
}