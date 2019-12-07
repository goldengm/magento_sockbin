<?php

class KingMo_CookieWishlist_Model_Wishlist extends Mage_Wishlist_Model_Wishlist {
    public function getDataForSave()
    {
        $data = array();
		if(Mage::getSingleton("customer/session")->isLoggedIn()) {
			$data[$this->_getResource()->getCustomerIdFieldName()] = $this->getCustomerId();
		} else {
			$data[$this->_getResource()->getCustomerIdFieldName()] = null;
		}
        $data['shared']       = (int) $this->getShared();
        $data['sharing_code'] = $this->getSharingCode();
        return $data;
    }
	
	public function setCookie() {
		$cookie = Mage::getModel("core/cookie");
		$cookie->set("CookieWishlistID", $this->getId(), 60 * 60 * 24 * 30);
	}
	
	public function getCookie() {
		$cookie = Mage::getModel("core/cookie");
		return $cookie->get("CookieWishlistID", $this->getId(), 60 * 60 * 24 * 30);
	}
	
	public function renewCookie() {
		$cookie = Mage::getModel("core/cookie");
		$cookie->renew("CookieWishlistID", 60 * 60 * 24 * 30);
	}
	
	public function loadFromCookie($create = false) {
		$id = $this->getCookie();
		if($id) {
			$this->load($id);
			$this->renewCookie();
		} else if($create) {
			$this->generateSharingCode();
			$this->setCustomerId(null);
			$this->save();
		}
		return $this;
	}
	
    public function save()
    {
        $this->_hasDataChanges = true;
        $savedWL = parent::save();
		$this->setCookie();
		return $savedWL;
    }
}