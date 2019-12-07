<?php
class Snowdog_Sociallogin_Block_Sociallogin_Sociallogin extends Apptha_Sociallogin_Block_Sociallogin
{

/**
 * preparing the social login pop-up layout
 *
 * Include the social login js file
 */

    public function _prepareLayout() {

        if (Mage::getStoreConfig('sociallogin/general/enable_sociallogin') == 1 && !Mage::helper('customer')->isLoggedIn()) {
            $this->getLayout()->getBlock('head')->addJs('snowsociallogin/sociallogin.js');
        }

        return Mage_Core_Block_Abstract::_prepareLayout();
    }
}
			