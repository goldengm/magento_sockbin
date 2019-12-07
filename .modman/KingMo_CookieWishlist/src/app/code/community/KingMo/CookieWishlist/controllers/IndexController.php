<?php
require_once Mage::getModuleDir('controllers', "Mage_Wishlist").DS."IndexController.php";
class KingMo_CookieWishlist_IndexController extends Mage_Wishlist_IndexController {
    protected $_skipAuthentication = true;
	
    protected function _getWishlist($wishlistId = null)
    {
        $wishlist = Mage::registry('wishlist');
        if ($wishlist) {
            return $wishlist;
        }

        try {
            if (!$wishlistId) {
                $wishlistId = $this->getRequest()->getParam('wishlist_id');
            }
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            /* @var Mage_Wishlist_Model_Wishlist $wishlist */
            $wishlist = Mage::getModel('wishlist/wishlist');
			
            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } else if ($customerId) {
                $wishlist->loadByCustomer($customerId, true);
            } else {
				$wishlist->loadFromCookie(true);
			}

            if (!$wishlist->getId()) {
                $wishlist = null;
                Mage::throwException(
                    Mage::helper('wishlist')->__("Requested wishlist doesn't exist")
                );
            }

            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
            return false;
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException($e,
                Mage::helper('wishlist')->__('Wishlist could not be created.')
            );
            return false;
        }

        return $wishlist;
    }
	
    public function indexAction()
    {
        if (!$this->_getWishlist()) {
            return $this->norouteAction();
        }
        $this->loadLayout();

        $session = Mage::getSingleton('customer/session');
        $block   = $this->getLayout()->getBlock('customer.wishlist');
        $referer = $session->getAddActionReferer(true);
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
            if ($referer) {
                $block->setRefererUrl($referer);
            }
        }

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('wishlist/session');

        $this->renderLayout();
    }
	
    public function shareAction()
    {
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			$this->_getWishlist();
			$this->loadLayout();
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('wishlist/session');
			$this->renderLayout();
		} else {
			Mage::getSingleton('core/session')->addNotice('You have to be logged in to share your wishlist!');
			$this->_redirect('customer/account/login/');
			return;
		}
    }
}