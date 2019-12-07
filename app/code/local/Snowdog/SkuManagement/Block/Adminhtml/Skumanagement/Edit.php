<?php

/**
 * Class Snowdog_SkuManagement_Block_Adminhtml_Skumanagement_Edit
 */
class Snowdog_SkuManagement_Block_Adminhtml_Skumanagement_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Snowdog_SkuManagement_Block_Adminhtml_Skumanagement_Edit constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId   = 'id';
        $this->_blockGroup = 'snowskumanagement';
        $this->_controller = 'adminhtml_skumanagement';
        $this->_mode       = 'edit';
        $this->removeButton('delete');
        $this->removeButton('reset');
        $this->removeButton('back');

        $this->_updateButton(
            'save',
            'label',
            Mage::helper('snowskumanagement')->__('Upload and Import')
        );
    }

    /**
     * @return mixed
     */
    public function getHeaderText()
    {
        return Mage::helper('snowskumanagement')->__('Import Skus Management');
    }

}