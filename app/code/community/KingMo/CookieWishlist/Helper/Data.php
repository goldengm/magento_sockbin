<?php
class KingMo_CookieWishlist_Helper_Data extends Mage_Wishlist_Helper_Data {
    public function getWishlist()
    {
        if (is_null($this->_wishlist)) {
            if (Mage::registry('shared_wishlist')) {
                $this->_wishlist = Mage::registry('shared_wishlist');
            } elseif (Mage::registry('wishlist')) {
                $this->_wishlist = Mage::registry('wishlist');
            } else {
                $this->_wishlist = Mage::getModel('wishlist/wishlist');
                if ($this->getCustomer()) {
                    $this->_wishlist->loadByCustomer($this->getCustomer());
                } else {
					$this->_wishlist->loadFromCookie();
				}
            }
        }
        return $this->_wishlist;
    }
	
    public function calculate()
    {
        $session = $this->_getCustomerSession();
        $count = 0;
		$collection = $this->getWishlistItemCollection()->setInStockFilter(true);
		if (Mage::getStoreConfig(self::XML_PATH_WISHLIST_LINK_USE_QTY)) {
			$count = $collection->getItemsQty();
		} else {
			$count = $collection->getSize();
		}
		$session->setWishlistDisplayType(Mage::getStoreConfig(self::XML_PATH_WISHLIST_LINK_USE_QTY));
		$session->setDisplayOutOfStockProducts(
			Mage::getStoreConfig(self::XML_PATH_CATALOGINVENTORY_SHOW_OUT_OF_STOCK)
		);
        $session->setWishlistItemCount($count);
        Mage::dispatchEvent('wishlist_items_renewed');
        return $this;
    }
}