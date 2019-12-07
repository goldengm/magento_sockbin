<?php

class Snowdog_AmazonProducts_Block_Adminhtml_Amazonskus_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_objectId   = 'id';
        $this->_blockGroup = 'snowamazonproducts';
        $this->_controller = 'adminhtml_amazonskus';
        $this->_mode       = 'edit';
        $this->removeButton('delete');
        $this->removeButton('reset');
        $this->removeButton('back');

        $this->_updateButton('save', 'label',
            Mage::helper('snowamazonproducts')->__('Upload and Import'));
    }

    public function getHeaderText()
    {
        return Mage::helper('snowamazonproducts')->__('Import Amazon Skus');
    }
}