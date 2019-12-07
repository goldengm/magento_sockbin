<?php

/**
 * Class Snowdog_SkuManagement_Block_Adminhtml_Catalog_Product_Edit_Tab
 */
class Snowdog_SkuManagement_Block_Adminhtml_Catalog_Product_Edit_Tab
    extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
     * Snowdog_SkuManagement_Block_Adminhtml_Catalog_Product_Edit_Tab constructor
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        parent::__construct($args);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function canShowTab()
    {
        $currentProduct = Mage::registry('current_product');

        return
            (
                ($this->getRequest()->getActionName() === 'new')
                && (!$this->getRequest()->getParam('set'))
                || ($currentProduct->getTypeId() != 'simple')
            )
                ? false : true;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Sku Management');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Sku Management');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('*/*/skumanagement', array('_current' => true));
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax';
    }

}
