<?php
require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';
class Snowdog_Orderupdate_Adminhtml_OrderupdateController extends Mage_Adminhtml_Sales_OrderController
{

	public function updateorderfieldAction()
    {
    	if ($order = $this->_initOrder()) {
    		$order->setCustomerEmail($this->getRequest()->getParam('value'))->save();
    	}
    	$this->getResponse()->setBody('<strong>'.$order->getCustomerEmail().'</strong>');
    }

	
}