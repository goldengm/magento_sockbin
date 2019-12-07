<?php

/**
 * Class Snowdog_SuccessRegistration_IndexController
 */
class Snowdog_SuccessRegistration_IndexController
    extends Mage_Core_Controller_Front_Action
{

    /**
     * Register a customer and link history orders to itself
     */
    public function registerPostAction()
    {
        $password = $this->getRequest()->getPost('sockbin_password', false);

        // Customer decides to register
        if ($password) {
            $lastOrderId = Mage::getSingleton('checkout/session')
                ->getLastOrderId();

            if ($lastOrderId) {
                /*  @var $order Mage_Sales_Model_Order */
                $orderModel = Mage::getModel('sales/order');
                $order = $orderModel->load($lastOrderId);

                if ($order) {
                    $customerEmail = $order->getCustomerEmail();

                    if ($customerEmail) {
                        /* @var $customerModel Mage_Customer_Model_Customer */
                        $customerModel = Mage::getModel('customer/customer');
                        $currentWebSiteId = Mage::app()->getStore()->getWebsiteId();
                        $customer = $customerModel
                            ->setWebsiteId($currentWebSiteId)
                            ->loadByEmail($customerEmail);

                        // If customer wasn't registered previously, create account
                        if (!$customer->getId()) {
                            $firstName = $order->getCustomerFirstname();
                            $lastName = $order->getCustomerLastname();
                            $middleName = $order->getCustomerMiddlename();

                            $customerModel
                                ->setData('email', $customerEmail)
                                ->setData('password', $password)
                                ->setData('firstname', $firstName)
                                ->setData('middlename', $middleName)
                                ->setData('lastname', $lastName);

                            try {
                                $customerModel->save();

                                // Customer saved correctly, create its addresses
                                if ($customerModel->getId()) {
                                    $billingAddress = $order->getBillingAddress();
                                    $shippingAddress = $order->getShippingAddress();

                                    $success = $this
                                        ->saveCustomerAddresses(
                                            $customerModel->getId(),
                                            $billingAddress,
                                            $shippingAddress
                                        );

                                    // Time to link orders with customer
                                    /* @var $successModel Snowdog_SuccessRegistration_Model_Registration */
                                    $successModel = Mage::getModel('snowsuccessregistration/registration');
                                    $ordersNotLinked = $successModel
                                        ->getCollection()
                                        ->addFieldToFilter('customer_email', $customerEmail);

                                    foreach ($ordersNotLinked as $noLinked) {
                                        $order = $orderModel
                                            ->load($noLinked->getOrderId(), 'increment_id');

                                        if ($order->getId() && !$order->getCustomerId()) {
                                            $order->setCustomerId($customerModel->getId());

                                            try {
                                                $order->save();
                                                $noLinked->delete();
                                            } catch (Exception $e) {
                                                Mage::getSingleton('core/session')
                                                    ->addError(
                                                        Mage::helper('snowsuccessregistration')
                                                        ->__("Couldn't save order: " . $e->getMessage())
                                                    );

                                                $this->_redirect('/');
                                                return;
                                            } catch (Exception $e) {
                                                Mage::getSingleton('core/session')
                                                    ->addError(
                                                        Mage::helper('snowsuccessregistration')
                                                            ->__("Couldn't delete not linked order: " . $e->getMessage())
                                                    );

                                                $this->_redirect('/');
                                                return;
                                            }
                                        }
                                    }

                                }
                            } catch (Exception $e) {
                                Mage::getSingleton('core/session')
                                    ->addError(
                                        Mage::helper('snowsuccessregistration')
                                            ->__("Customer couldn't be created: ") . $e->getMessage()
                                    );

                                $this->_redirect('/');
                                return;
                            }
                        } else {
                            Mage::getSingleton('core/session')
                                ->addError(
                                    Mage::helper('snowsuccessregistration')
                                        ->__("There is an existing account for this email")
                                );

                            $this->_redirect('/');
                            return;
                        }
                    } else {
                        Mage::getSingleton('core/session')
                            ->addError(
                                Mage::helper('snowsuccessregistration')
                                    ->__("Customer Email not found in order")
                            );

                        $this->_redirect('/');
                        return;
                    }
                } else {
                    Mage::getSingleton('core/session')
                        ->addError(
                            Mage::helper('snowsuccessregistration')
                                ->__("The order to be linked doesn't exist")
                        );

                    $this->_redirect('/');
                    return;
                }
            } else {
                Mage::getSingleton('core/session')
                    ->addError(
                        Mage::helper('snowsuccessregistration')
                            ->__("There is no order to be linked with")
                    );

                $this->_redirect('/');
                return;
            }
        } else {
            Mage::getSingleton('core/session')
                ->addError(
                    Mage::helper('snowsuccessregistration')
                        ->__("Password is a required field")
                );

            $this->_redirect('/');
            return;
        }

        Mage::getSingleton('core/session')
            ->addSuccess(
                Mage::helper('snowsuccessregistration')
                    ->__("You have created an account successfully")
            );

        $this->_redirect('/');
        return;
    }

    /**
     * Save customer addresses
     *
     * @param $customerId
     * @param $billingAddress
     * @param $shippingAddress
     *
     * @return bool
     */
    private function saveCustomerAddresses($customerId, $billingAddress, $shippingAddress = null)
    {
        $customerBilling = [
            'firstname' => $billingAddress->getData('firstname'),
            'lastname' => $billingAddress->getData('lastname'),
            'middlename' => $billingAddress->getData('middlename'),
            'street' => $billingAddress->getData('street'),
            'city' => $billingAddress->getData('city'),
            'region_id' => $billingAddress->getData('region_id'),
            'region' => $billingAddress->getData('region'),
            'postcode' => $billingAddress->getData('postcode'),
            'country_id' => $billingAddress->getData('country_id'),
            'telephone' => $billingAddress->getData('telephone'),
        ];

        /* @var $billingAddressObject Mage_Customer_Model_Address */
        $billingAddressObject = Mage::getModel('customer/address');
        $billingAddressObject->setData($customerBilling);
        $billingAddressObject->setCustomerId($customerId);
        $billingAddressObject->setIsDefaultBilling('1');
        $billingAddressObject->setSaveInAddressBook('1');

        if (!$shippingAddress->getId()) {
            $billingAddress->setIsDefaultShipping('1');
        }

        try {
            $billingAddressObject->save();
        } catch (Exception $e) {
            Mage::getSingleton('core/session')
                ->addError(
                    Mage::helper('snowsuccessregistration')
                    ->__("Couldn't save customer billing address: ") . $e->getMessage()
                );

            return false;
        }

        if ($shippingAddress) {
            $customerShipping = [
                'firstname' => $shippingAddress->getData('firstname'),
                'lastname' => $shippingAddress->getData('lastname'),
                'middlename' => $shippingAddress->getData('middlename'),
                'street' => $shippingAddress->getData('street'),
                'city' => $shippingAddress->getData('city'),
                'region_id' => $shippingAddress->getData('region_id'),
                'region' => $shippingAddress->getData('region'),
                'postcode' => $shippingAddress->getData('postcode'),
                'country_id' => $shippingAddress->getData('country_id'),
                'telephone' => $shippingAddress->getData('telephone'),
            ];

            /* @var $shippingAddressObject Mage_Customer_Model_Address */
            $shippingAddressObject = Mage::getModel('customer/address');
            $shippingAddressObject->setData($customerShipping);
            $shippingAddressObject->setCustomerId($customerId);
            $shippingAddressObject->setIsDefaultShipping('1');
            $shippingAddressObject->setSaveInAddressBook('1');

            try {
                $shippingAddressObject->save();
            } catch (Exception $e) {
                Mage::getSingleton('core/session')
                    ->addError(
                        Mage::helper('snowsuccessregistration')
                            ->__("Couldn't save customer shipping address: ") . $e->getMessage()
                    );

                return false;
            }
        }

        return true;
    }

}