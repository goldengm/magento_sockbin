<?php
class Snowdog_Pobox_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getAjaxUrl()
    {
        return Mage::getUrl('snowpobox/index/savepobox');
    }

    public function getPoBoxStatus()
    {
        return Mage::getSingleton('core/session')->getPoBox();
    }


    public function getTotal()
    {
        $quote = Mage::getModel('checkout/session')->getQuote();

        return Mage::helper('core')->currency($quote->getGrandTotal(), true, false);
    }
}
	 