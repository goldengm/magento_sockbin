<?php

require_once 'Mage/Checkout/controllers/CartController.php';

class Hardik_Ajaxcart_Checkout_CartController extends Mage_Checkout_CartController {

    function ajaxCouponPostAction() {
        // if not ajax have parent deal with result 
        if (!isset($_POST['ajax'])) {
            parent::ajaxCouponPostAction();
            return;
        }

        $msg['txt'] = '';
        $msg['type'] = '';

        $couponCode = (string) $this->getRequest()->getParam('coupon_code');

        // if cancel - remove discount
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
            $msg['type'] = 'success';
            $msg['txt'] = $this->__('Coupon was canceled.');
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                    ->collectTotals()
                    ->save();
            $msg['block'] = $this->getUpdatedBlock();
            $layout = $this->getLayout();
            $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
            return $this->getResponse()->setBody(json_encode($msg));
        }

        $oldCouponCode = $this->_getQuote()->getCouponCode();

        //if code is empty
        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $couponCode = '';
            $msg['type'] = 'error';
            $msg['txt'] = $this->__('Please enter your code.');
            $msg['block'] = $this->getUpdatedBlock();
            $layout = $this->getLayout();
            $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
            return $this->getResponse()->setBody(json_encode($msg));
        } else if (!strlen($couponCode)) {
            $msg['type'] = 'error';
            $msg['txt'] = $this->__('Enter new coupon code or cancel this one.');
            $msg['block'] = $this->getUpdatedBlock();
            $layout = $this->getLayout();
            $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
            return $this->getResponse()->setBody(json_encode($msg));
        }

        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                    ->collectTotals()
                    ->save();

            if ($couponCode) {
                if ($couponCode == $this->_getQuote()->getCouponCode()) {
                    $this->_getSession()->addSuccess(
                            $this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))
                    );
                    $msg['txt'] = $this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode));
                    $msg['type'] = 'success';
                } else {
                    $msg['txt'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
                    $msg['type'] = 'error';
                }
            } else {
                $this->_getSession()->addSuccess($this->__('Coupon code was canceled.'));
                $msg['txt'] = $this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode));
                $msg['type'] = 'success';
            }
        } catch (Mage_Core_Exception $e) {
            $msg['txt'] = $e->getMessage();
            $msg['type'] = 'error';
        } catch (Exception $e) {
            $msg['txt']['txt'] = $this->__('Cannot apply the coupon code.');
            $msg['type'] = 'error';
            Mage::logException($e);
        }

        $msg['block'] = $this->getUpdatedBlock();
        $layout = $this->getLayout();
        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
        return $this->getResponse()->setBody(json_encode($msg));

    }

    protected function getUpdatedBlock()
    {
        $layout = $this->getLayout();
        $block['header-cart'] = $layout->createBlock('checkout/cart_sidebar')->setTemplate('sideminicart/checkout/cart/minicart/items.phtml')->toHtml();
        $block['minicart-totals'] = $layout->createBlock('checkout/cart_totals')->setTemplate('sideminicart/checkout/cart/totals.phtml')->toHtml();
        $block['minicart-coupon'] = $layout->createBlock('checkout/cart_coupon')->setTemplate('sideminicart/checkout/cart/coupon.phtml')->toHtml();
        $block['minicart-top-msg'] = $this->getLayout()->createBlock('cms/block')->setBlockId('minicart-top-msg')->toHtml();
        return $block;

    }

    /**
     * Add product to shopping cart action
     */
    public function addAction() {
        $cart = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                                array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }


            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            $this->getLayout()->getUpdate()->addHandle('ajaxcart');
            $this->loadLayout();

            Mage::dispatchEvent('checkout_cart_add_product_complete', array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            $_response = Mage::getModel('ajaxcart/response');
            $_response->setError(true);

            $messages = array_unique(explode("\n", $e->getMessage()));
            $json_messages = array();
            foreach ($messages as $message) {
                $json_messages[] = Mage::helper('core')->escapeHtml($message);
            }

            $_response->setMessages($json_messages);

            $url = $this->_getSession()->getRedirectUrl(true);

            $_response->send();
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);

            $_response = Mage::getModel('ajaxcart/response');
            $_response->setError(true);
            $_response->setMessage($this->__('Cannot add the item to shopping cart.'));
            $_response->send();
        }
    }

    /**
     * Update product configuration for a cart item
     */
    public function updateItemOptionsAction() {
        $cart = $this->_getCart();
        $id = (int) $this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();

        if (!isset($params['options'])) {
            $params['options'] = array();
        }
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                                array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $quoteItem = $cart->getQuote()->getItemById($id);
            if (!$quoteItem) {
                Mage::throwException($this->__('Quote item is not found.'));
            }

            $item = $cart->updateItem($id, new Varien_Object($params));
            if (is_string($item)) {
                Mage::throwException($item);
            }
            if ($item->getHasError()) {
                Mage::throwException($item->getMessage());
            }

            $related = $this->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            $this->getLayout()->getUpdate()->addHandle('ajaxcart');
            $this->loadLayout();

            Mage::dispatchEvent('checkout_cart_update_item_complete', array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was updated in your shopping cart.', Mage::helper('core')->htmlEscape($item->getProduct()->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            $_response = Mage::getModel('ajaxcart/response');
            $_response->setError(true);

            $messages = array_unique(explode("\n", $e->getMessage()));
            $json_messages = array();
            foreach ($messages as $message) {
                $json_messages[] = Mage::helper('core')->escapeHtml($message);
            }

            $_response->setMessages($json_messages);

            $url = $this->_getSession()->getRedirectUrl(true);

            $_response->send();
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update the item.'));
            Mage::logException($e);

            $_response = Mage::getModel('ajaxcart/response');
            $_response->setError(true);
            $_response->setMessage($this->__('Cannot update the item.'));
            $_response->send();
        }
    }

    /**
     * Delete shoping cart item action
     */
    public function deleteAction() {
        $id = (int) $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->_getCart()->removeItem($id)
                        ->save();
            } catch (Exception $e) {
                $_response = Mage::getModel('ajaxcart/response');
                $_response->setError(true);
                $_response->setMessage($this->__('Cannot remove the item.'));
                $_response->send();

                Mage::logException($e);
            }
        }

        $_response = Mage::getModel('ajaxcart/response');

        $_response->setMessage($this->__('Item was removed.'));

        //append updated blocks
        $this->getLayout()->getUpdate()->addHandle('ajaxcart');
        $this->loadLayout();

        $_response->addUpdatedBlocks($_response);

        $_response->send();
    }

}