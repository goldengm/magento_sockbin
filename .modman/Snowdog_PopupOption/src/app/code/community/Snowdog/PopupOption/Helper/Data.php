<?php

class Snowdog_PopupOption_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_SETTINGS_POPUPOPTION_GENERAL_STATUS = 'popupoption/general/enable';
    const XML_PATH_SETTINGS_POPUPOPTION_GENERAL_MOBILE_ID = 'popupoption/general/top_block_id_mobile';
    const XML_PATH_SETTINGS_POPUPOPTION_GENERAL_BLOCK_ID = 'popupoption/general/top_block_id';
    const XML_PATH_SETTINGS_POPUPOPTION_GENERAL_EXPIRY = 'popupoption/general/cookie_expiry';
    const XML_PATH_SETTINGS_POPUPOPTION_GENERAL_RESTART = 'popupoption/general/cookie_reset';
    const XML_PATH_SETTINGS_POPUPOPTION_TEXT = 'popupoption/general/text_field_sing_up';
    const XML_PATH_SETTINGS_POPUPOPTION_REFRESH_PAGE = 'popupoption/general/refresh_page';

    public function getStoreId()
    {
        return Mage::app()->getStore()->getStoreId();
    }

    public function getPopupStatus()
    {
        return Mage::getStoreConfig(self::XML_PATH_SETTINGS_POPUPOPTION_GENERAL_STATUS, $this->getStoreId());
    }

    public function getMobileStaticBlokcId()
    {
        return Mage::getStoreConfig(self::XML_PATH_SETTINGS_POPUPOPTION_GENERAL_MOBILE_ID, $this->getStoreId());
    }

    public function getMobileBlock()
    {
        return Mage::app()->getLayout()->createBlock('cms/block')->setBlockId($this->getMobileStaticBlokcId())->toHtml();
    }

    public function getStaticBlokcId()
    {
        return Mage::getStoreConfig(self::XML_PATH_SETTINGS_POPUPOPTION_GENERAL_BLOCK_ID, $this->getStoreId());
    }

    public function getBlock()
    {
        return Mage::app()->getLayout()->createBlock('cms/block')->setBlockId($this->getStaticBlokcId())->toHtml();
    }

    public function getSingupText()
    {
        return Mage::getStoreConfig(self::XML_PATH_SETTINGS_POPUPOPTION_TEXT, $this->getStoreId());
    }

    public function getExpiry()
    {
        $days = Mage::getStoreConfig(self::XML_PATH_SETTINGS_POPUPOPTION_GENERAL_EXPIRY, $this->getStoreId());
        if ($days) {
            return $days;
        } else {
            return null;
        }
    }

    public function getRefreshPage()
    {
        $refreshPage =  Mage::getStoreConfig(self::XML_PATH_SETTINGS_POPUPOPTION_REFRESH_PAGE, $this->getStoreId());
        if($refreshPage == 0 || $refreshPage == 1){
            return 0;
        }else{
            return $refreshPage - 1;
        }
    }

    public function getLastPageView()
    {
        if(isset($_SERVER['HTTP_REFERER'])) {
            $lastViewPage = $_SERVER['HTTP_REFERER'];
        }else{
            $lastViewPage = null;
        }
        $baseUrl = Mage::getBaseUrl();

        if (strpos($lastViewPage, $baseUrl) !== false) {
            return true;
        } else {
            return false;
        }
    }

    public function getLastRestart()
    {
        $date = Mage::getStoreConfig(self::XML_PATH_SETTINGS_POPUPOPTION_GENERAL_RESTART, $this->getStoreId());
        if ($date) {
            return $date;
        } else {
            return 0;
        }
    }
}
	 