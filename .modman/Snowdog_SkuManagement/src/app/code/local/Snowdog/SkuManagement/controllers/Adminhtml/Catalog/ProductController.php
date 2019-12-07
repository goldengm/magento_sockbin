<?php

require_once(Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Catalog' . DS . 'ProductController.php');

/**
 * Class Snowdog_SkuManagement_Adminhtml_Catalog_ProductController
 */
class Snowdog_SkuManagement_Adminhtml_Catalog_ProductController
    extends Mage_Adminhtml_Catalog_ProductController
{

    /**
     * Get linked products grid and serializer block
     */
    public function skumanagementAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.skumanagement')
                ->setProductsSkumanagement(
                    $this->getRequest()->getPost('products_skumanagement', null)
                );
        $this->renderLayout();
    }

    /**
     * Get linked products grid
     */
    public function skumanagementGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.skumanagement')
                ->setProductsSkumanagement(
                    $this->getRequest()->getPost('products_skumanagement', null)
                );
        $this->renderLayout();
    }

}
