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

class Webtex_Fba_Block_Adminhtml_Product_Simple_FbaTab extends Mage_Adminhtml_Block_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('webtex/fba/product/tab.phtml');
    }

    /**
     * Retrieve currently edited product model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }


    protected function _prepareLayout()
    {
        $syncButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id' => 'sync_button',
                'label' => Mage::helper('catalog/product')->__('Sync Inventory'),
                'class' => 'save',
                'on_click' => 'setLocation(\'' . $this->_getSendQueryUrl() . '\')'
            ));
        $this->setChild('sync_button', $syncButton);
        return parent::_prepareLayout();
    }

    public function getInfo()
    {
        $info = array();
        /** @var $assigned Webtex_Fba_Model_Mws_Product */
        $assigned = Mage::getModel('mws/product')->loadByProduct($this->_getProduct());
        if ($assigned->getId()) {
            $info['in_stock_qty']['name'] = $this->getHelper('catalog/product')->__('Qty In Stock:');
            $info['in_stock_qty']['value'] = (string)$assigned->getInStockQty();
            $info['total_qty']['name'] = $this->getHelper('catalog/product')->__('Total Qty:');
            $info['total_qty']['value'] = (string)$assigned->getTotalQty();
            $info['magento_qty']['name'] = $this->getHelper('catalog/product')->__('Ordered in Magento:');
            $info['magento_qty']['value'] = (string)$assigned->getMagentoOrderedQty();
            $info['change_date']['name'] = $this->getHelper('catalog/product')->__('Change Date:');
            $info['change_date']['value'] = (string)$assigned->getChangeDate();
        }
        return $info;
    }

    public function getTabLabel()
    {
        return Mage::helper('catalog')->__('Fulfillment By Amazon');
    }

    public function getTabTitle()
    {
        return Mage::helper('catalog')->__('Fulfillment By Amazon');
    }

    public function canShowTab()
    {
        return $this->_getProduct()->getIsFba();
    }

    public function isHidden()
    {
        return !$this->_getProduct()->getIsFba();
    }

    protected function _getSendQueryUrl()
    {
        return $this->getUrl('fba/adminhtml_index/syncProductBySku', array('product_sku' => $this->_getProduct()->getSku(), 'product_id' => $this->_getProduct()->getEntityId()));
    }
}
