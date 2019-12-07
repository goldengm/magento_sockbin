<?php

class Snowdog_PopupOption_Adminhtml_PopupOptionController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $timestamp = round(microtime(true) * 1000);

        $storeId = Mage::app()->getStore()->getStoreId();

        Mage::getConfig()->saveConfig('popupoption/general/cookie_reset', $timestamp, 'default', $storeId);

        $this->_redirectReferer();
    }
}
