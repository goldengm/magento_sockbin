<?php

class Snowdog_AmazonProducts_Block_Adminhtml_Catalog_Product_Edit_Tab
    extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct(array $args = array())
    {
        parent::__construct($args);
    }

    public function canShowTab()
    {
        $currentProduct = Mage::registry('current_product');
        return (($this->getRequest()->getActionName() === 'new') && (!$this->getRequest()->getParam('set')) || ($currentProduct->getTypeId() != 'simple')) ? false : true;
    }

    public function getTabLabel()
    {
        return $this->__('Amazon Products');
    }

    public function getTabTitle()
    {
        return $this->__('Amazon Products');
    }

    public function isHidden()
    {
        return false;
    }

    public function getTabUrl()
    {
        return $this->getUrl('*/*/amazon', array('_current' => true));
    }

    public function getTabClass()
    {
        return 'ajax';
    }

}
