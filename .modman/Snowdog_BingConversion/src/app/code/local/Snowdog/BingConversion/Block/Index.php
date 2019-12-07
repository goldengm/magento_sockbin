<?php

/**
 * Class Snowdog_BingConversion_Block_Index
 */
class Snowdog_BingConversion_Block_Index extends Mage_Core_Block_Template
{
    /**
     * Get order amount
     */
    public function getAmount()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        if($orderId) {
            $order = Mage::getSingleton('sales/order')->loadByIncrementId($orderId);

            return $order->getGrandTotal();
        }

        return null;
    }
}