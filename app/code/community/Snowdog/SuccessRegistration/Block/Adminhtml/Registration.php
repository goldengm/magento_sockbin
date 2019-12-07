<?php

/**
 * Class Snowdog_SuccessRegistration_Block_Adminhtml_Registration
 */
class Snowdog_SuccessRegistration_Block_Adminhtml_Registration
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    
    /**
     * Snowdog_SuccessRegistration_Block_Adminhtml_Registration constructor.
     */
    public function __construct()
    {
        $this->_blockGroup = 'snowsuccessregistration';
        $this->_controller = 'adminhtml_registration';
        $this->_headerText = Mage::helper('snowsuccessregistration')->__('Registration');

        parent::__construct();
        $this->_removeButton('add');
    }
}
