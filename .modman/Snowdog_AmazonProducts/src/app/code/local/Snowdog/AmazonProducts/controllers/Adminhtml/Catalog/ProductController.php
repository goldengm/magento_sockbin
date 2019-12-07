<?php

require_once(Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Catalog' . DS . 'ProductController.php');

class Snowdog_AmazonProducts_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController {

    /**
     * Get customd products grid and serializer block
     */
    public function amazonAction() {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.custom')
                ->setProductsCustom($this->getRequest()->getPost('products_amazon', null));
        $this->renderLayout();
    }

    /**
     * Get custom products grid
     */
    public function amazonGridAction() {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.custom')
                ->setProductsCustom($this->getRequest()->getPost('products_amazon', null));
        $this->renderLayout();
    }

}
