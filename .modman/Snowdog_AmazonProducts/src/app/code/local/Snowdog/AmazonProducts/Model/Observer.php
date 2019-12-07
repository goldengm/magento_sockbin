<?php

class Snowdog_AmazonProducts_Model_Observer extends Varien_Object {

    public function productPrepareSave($observer) {
        $event = $observer->getEvent();
        $product = $event->getProduct();
        $request = $event->getRequest();
        $links = $request->getPost('links');

        if (isset($links['custom']) && !$product->getAmazonReadonly()) {
            $product->setAmazonLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['custom']));
        }
    }

    public function productDuplicate($observer) {
        $event = $observer->getEvent();
        $currentProduct = $event->getCurrentProduct();
        $newProduct = $event->getNewProduct();
        $data = array();
        $currentProduct->getLinkInstance()->useAmazonLinks();
        $attributes = array();
        
        foreach ($currentProduct->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        
        foreach ($currentProduct->getAmazonLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        
        $newProduct->setAmazonLinkData($data);
    }
    


}
