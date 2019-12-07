<?php

/**
 * Class Snowdog_SuccessRegistration_Model_Observer
 */
class Snowdog_SuccessRegistration_Model_Observer
{

    /**
     * Check if customer is registered, otherwise save it in registration table
     *
     * @param Varien_Event_Observer $observer
     */
    public function checkIfCustomerIsGuest(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $customerEmail = $order->getCustomerEmail();

        if ($customerEmail) {
            /* @var $customerModel Mage_Customer_Model_Customer */
            $customerModel = Mage::getModel('customer/customer');
            $currentWebSiteId = Mage::app()->getStore()->getWebsiteId();
            $customer = $customerModel
                ->setWebsiteId($currentWebSiteId)
                ->loadByEmail($customerEmail);

            // If customer is not registered, save in our table
            if (!$customer || !$customer->getId()) {
                /* @var $successRegistrationModel Snowdog_SuccessRegistration_Model_Registration */
                $successRegistrationModel = Mage::getModel('snowsuccessregistration/registration');

                $successRegistrationModel
                    ->setCustomerEmail($customerEmail)
                    ->setOrderId($order->getIncrementId());

                try {
                    $successRegistrationModel->save();
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')
                        ->addError(
                            Mage::helper('snowsuccessregistration')
                                ->__("Customer email couldn't be saved in registration table")
                        );
                }
            }
        }
    }

    /**
     * Check if customer has orders as a guest and link them after registration
     *
     * @param Varien_Event_Observer $observer
     */
    public function customerRegisterSuccess(Varien_Event_Observer $observer)
    {
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = $observer->getCustomer();
        /*  @var $order Mage_Sales_Model_Order */
        $orderModel = Mage::getModel('sales/order');
        /* @var $successModel Snowdog_SuccessRegistration_Model_Registration */
        $successModel = Mage::getModel('snowsuccessregistration/registration');
        $ordersNotLinked = $successModel
            ->getCollection()
            ->addFieldToFilter('customer_email', $customer->getEmail());
        $someLinked = false;

        /* @var $noLinked Snowdog_SuccessRegistration_Model_Registration */
        foreach ($ordersNotLinked as $noLinked) {
            $order = $orderModel
                ->load($noLinked->getOrderId(), 'increment_id');

            if ($order->getId() && !$order->getCustomerId()) {
                $order->setCustomerId($customer->getId());

                try {
                    $order->save();
                    $noLinked->delete();
                    $someLinked = true;
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')
                        ->addError(
                            Mage::helper('snowsuccessregistration')
                                ->__("Couldn't link orders to customer: " . $e->getMessage())
                        );
                    return;
                }
            }
        }

        if ($someLinked) {
            Mage::getSingleton('core/session')
                ->addSuccess(
                    Mage::helper('snowsuccessregistration')
                        ->__('We have linked your order history with your account')
                );
        }
    }
}