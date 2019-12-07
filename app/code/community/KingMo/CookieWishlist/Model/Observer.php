<?php
class KingMo_CookieWishlist_Model_Observer extends Mage_Wishlist_Model_Observer
{
    public function processCartUpdateBefore($observer)
    {
        $cart = $observer->getEvent()->getCart();
        $data = $observer->getEvent()->getInfo();
        $productIds = array();
		$wishlist = Mage::getModel("wishlist/wishlist");
		
		if(Mage::getSingleton("customer/session")->isLoggedIn()) {
			$wishlist->loadByCustomer($cart->getQuote()->getCustomerId());
		} else if ($wishlist->getCookie()) {
			$wishlist->loadFromCookie();
		} else {
			$wishlist = false;
		}
        if (!$wishlist) {
            return $this;
        }
		
        foreach ($data as $itemId => $itemInfo) {
            if (!empty($itemInfo['wishlist'])) {
                if ($item = $cart->getQuote()->getItemById($itemId)) {
                    $productId  = $item->getProductId();
                    $buyRequest = $item->getBuyRequest();

                    if (isset($itemInfo['qty']) && is_numeric($itemInfo['qty'])) {
                        $buyRequest->setQty($itemInfo['qty']);
                    }
                    $wishlist->addNewItem($productId, $buyRequest);

                    $productIds[] = $productId;
                    $cart->getQuote()->removeItem($itemId);
                }
            }
        }

        if (!empty($productIds)) {
            $wishlist->save();
            Mage::helper('wishlist')->calculate();
        }
        return $this;
    }

    public function processAddToCart($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $sharedWishlist = Mage::getSingleton('checkout/session')->getSharedWishlist();
        $messages = Mage::getSingleton('checkout/session')->getWishlistPendingMessages();
        $urls = Mage::getSingleton('checkout/session')->getWishlistPendingUrls();
        $wishlistIds = Mage::getSingleton('checkout/session')->getWishlistIds();
        $singleWishlistId = Mage::getSingleton('checkout/session')->getSingleWishlistId();

        if ($singleWishlistId) {
            $wishlistIds = array($singleWishlistId);
        }

        if (count($wishlistIds) && $request->getParam('wishlist_next')){
            $wishlistId = array_shift($wishlistIds);

            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $wishlist = Mage::getModel('wishlist/wishlist')
                        ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
            } else if ($sharedWishlist) {
                $wishlist = Mage::getModel('wishlist/wishlist')->loadByCode($sharedWishlist);
			} else if ($wishlist->getCookie()) {
				$wishlist = Mage::getModel("wishlist/wishlist")->loadFromCookie();
            } else {
                return;
            }


            $wishlist->getItemCollection()->load();

            foreach($wishlist->getItemCollection() as $wishlistItem){
                if ($wishlistItem->getId() == $wishlistId)
                    $wishlistItem->delete();
            }
            Mage::getSingleton('checkout/session')->setWishlistIds($wishlistIds);
            Mage::getSingleton('checkout/session')->setSingleWishlistId(null);
        }

        if ($request->getParam('wishlist_next') && count($urls)) {
            $url = array_shift($urls);
            $message = array_shift($messages);

            Mage::getSingleton('checkout/session')->setWishlistPendingUrls($urls);
            Mage::getSingleton('checkout/session')->setWishlistPendingMessages($messages);

            Mage::getSingleton('checkout/session')->addError($message);

            $observer->getEvent()->getResponse()->setRedirect($url);
            Mage::getSingleton('checkout/session')->setNoCartRedirect(true);
        }
    }

    public function customerLogin(Varien_Event_Observer $observer)
    {
		$customerId = Mage::getSingleton('customer/session')->getCustomerId();
		$customerWishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId);
		$cookieWishlist = Mage::getModel('wishlist/wishlist')->loadFromCookie();
		
		if($customerWishlist->getId() && $cookieWishlist->getId()) {
			if($customerWishlist->getId() != $cookieWishlist->getId()) {
				$alreadyAddedIds = array();
				foreach($customerWishlist->getItemCollection() as $customerWishlistItem) {
					$alreadyAddedIds[] = $customerWishlistItem->getProductId();
				}
				foreach($cookieWishlist->getItemCollection() as $cookieWishlistItem) {
					$product = $cookieWishlistItem->getProduct();
					if(!in_array($product->getId(), $alreadyAddedIds)) {
						$customerWishlist->addNewItem($product);
					}
				}
				$customerWishlist->save();
				$cookieWishlist->delete();
				Mage::getSingleton('core/session')->addSuccess('The products you have added to your wishlist while not being logged in have been added to your account\'s whishlist!');
			}
		} else if($cookieWishlist->getId()) {
			$cookieWishlist->setCustomerId($customerId);
			$cookieWishlist->save();
		}
		
		if($customerWishlist->getId() != false) {
			$customerWishlist->setCookie();
		}
		
        Mage::helper('wishlist')->calculate();

        return $this;
    }

    public function customerLogout(Varien_Event_Observer $observer)
    {
        Mage::helper('wishlist')->calculate();

        return $this;
    }

}
