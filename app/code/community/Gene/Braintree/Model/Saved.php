<?php
/**
 * Class Gene_Braintree_Model_Saved
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */ 
class Gene_Braintree_Model_Saved extends Mage_Core_Model_Abstract
{

    /**
     * The ID's associated with the two different payment methods
     */
    const SAVED_PAYPAL_ID = 1;
    const SAVED_CREDITCARD_ID = 2;

    private $savedAccounts = false;

    /**
     * Get the current customers saved cards
     *
     * @return array
     */
    public function getCustomerSavedPaymentMethods()
    {
        // Grab an instance of the customers session
        $customerSession = Mage::getSingleton('customer/session');

        // You can only store cards if you're logged in
        if($customerSession->isLoggedIn() && $customerSession->getCustomer()->getBraintreeCustomerId()) {

            if(!$this->savedAccounts) {

                // Grab a new instance of the wrapper
                $wrapper = Mage::getModel('gene_braintree/wrapper_braintree');

                // Init the braintree wrapper
                $wrapper->init();

                // Try and load the customer from Braintrees side
                if ($customer = $wrapper->getCustomer($customerSession->getCustomer()->getBraintreeCustomerId())) {

                    // Assign them into our model
                    $this->savedAccounts = array_merge($customer->creditCards, $customer->paypalAccounts);
                }

            }

            return $this->savedAccounts;

        }

        return false;
    }

    /**
     * Return a boolean value on whether the customer has a certain type of payment method
     *
     * @param bool $type
     *
     * @return bool|int
     */
    public function hasType($type = false)
    {
        // If no type is set just count the saved methods
        if(!$type) {
            if(!$this->getCustomerSavedPaymentMethods()) {
                return false;
            }
            return count($this->getCustomerSavedPaymentMethods());
        }

        // Check there are some saved accounts
        if($savedAccounts = $this->getCustomerSavedPaymentMethods()) {

            // Iterate through the saved accounts
            foreach ($savedAccounts as $savedAccount) {

                // Check which type we're after
                if ($type == Gene_Braintree_Model_Saved::SAVED_CREDITCARD_ID) {
                    if ($savedAccount instanceof Braintree_CreditCard) {
                        return true;
                    }
                } elseif ($type == Gene_Braintree_Model_Saved::SAVED_PAYPAL_ID) {
                    if ($savedAccount instanceof Braintree_PayPalAccount) {
                        return true;
                    }
                }

            }
        }

        return false;
    }

    /**
     * Return only those accounts which are a certain type

     * @param $type
     *
     * @return array
     */
    public function getSavedMethodsByType($type = false)
    {
        if(!$type) {
            return $this->getCustomerSavedPaymentMethods();
        }

        // Start up our new collection
        $savedDetails = array();

        if($this->getCustomerSavedPaymentMethods()) {
            foreach ($this->getCustomerSavedPaymentMethods() as $savedAccount) {

                // Check which type we're after
                if ($type == Gene_Braintree_Model_Saved::SAVED_CREDITCARD_ID) {
                    if ($savedAccount instanceof Braintree_CreditCard) {
                        $savedDetails[] = $savedAccount;
                    }
                } elseif ($type == Gene_Braintree_Model_Saved::SAVED_PAYPAL_ID) {
                    if ($savedAccount instanceof Braintree_PayPalAccount) {
                        $savedDetails[] = $savedAccount;
                    }
                }
            }
        }

        return $savedDetails;
    }

}