<?php

/**
 * Class Snowdog_SuccessRegistration_Adminhtml_GuestordersController
 */
class Snowdog_SuccessRegistration_Adminhtml_GuestordersController
    extends Mage_Adminhtml_Controller_Action
{

    /**
     * Render registration grid view
     */
    public function indexAction()
    {
        $this
            ->_title(
                $this->__('Customer Guest Orders')
            )
            ->_title(
                $this->__('Customer Guest Orders')
            );

        $this->loadLayout();
        $this->_setActiveMenu('customer/snowsuccessregistration');
        $this->_addContent($this->getLayout()->createBlock('snowsuccessregistration/adminhtml_registration'));
        $this->renderLayout();
    }

    /**
     * For ajax requests
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('snowsuccessregistration/adminhtml_registration_grid')->toHtml()
        );
    }

}