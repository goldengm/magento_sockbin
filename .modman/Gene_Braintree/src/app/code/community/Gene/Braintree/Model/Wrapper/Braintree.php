<?php

/**
 * Class Gene_Braintree_Model_Wrapper_Braintree
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_Braintree_Model_Wrapper_Braintree extends Mage_Core_Model_Abstract
{

    CONST BRAINTREE_ENVIRONMENT_PATH = 'payment/gene_braintree/environment';
    CONST BRAINTREE_MERCHANT_ID_PATH = 'payment/gene_braintree/merchant_id';
    CONST BRAINTREE_MERCHANT_ACCOUNT_ID_PATH = 'payment/gene_braintree/merchant_account_id';
    CONST BRAINTREE_PUBLIC_KEY_PATH = 'payment/gene_braintree/public_key';
    CONST BRAINTREE_PRIVATE_KEY_PATH = 'payment/gene_braintree/private_key';

    const BRAINTREE_MULTI_CURRENCY = 'payment/gene_braintree/multi_currency_enable';
    const BRAINTREE_MULTI_CURRENCY_MAPPING = 'payment/gene_braintree/multi_currency_mapping';

    /**
     * Store the customer
     *
     * @var Braintree_Customer
     */
    private $customer;

    /**
     * Store the Braintree ID
     *
     * @var int
     */
    private $braintreeId;

    /**
     * Used to track whether the payment methods are available
     *
     * @var bool
     */
    private $validated = null;

    /**
     * If we're using a mapped currency we need to charge the grand total, instead of the base
     *
     * @var bool
     */
    private $mappedCurrency = false;

    /**
     * Store whether or not we've init the environment yet
     *
     * @var bool
     */
    private $init = false;

    /**
     * Setup the environment
     *
     * @return $this
     */
    public function init($store = null)
    {
        if(!$this->init) {

            // Setup the various configuration variables
            Braintree_Configuration::environment(Mage::getStoreConfig(self::BRAINTREE_ENVIRONMENT_PATH, $store));
            Braintree_Configuration::merchantId(Mage::getStoreConfig(self::BRAINTREE_MERCHANT_ID_PATH, $store));
            Braintree_Configuration::publicKey(Mage::getStoreConfig(self::BRAINTREE_PUBLIC_KEY_PATH, $store));
            Braintree_Configuration::privateKey(Mage::getStoreConfig(self::BRAINTREE_PRIVATE_KEY_PATH, $store));

            // Set our flag
            $this->init = true;
        }

        return $this;
    }

    /**
     * Find a transaction
     *
     * @param $transactionId
     *
     * @throws Braintree_Exception_NotFound
     */
    public function findTransaction($transactionId)
    {
        return Braintree_Transaction::find($transactionId);
    }

    /**
     * If we're trying to charge a 3D secure card in the vault we need to build a special nonce
     *
     * @param $paymentMethodToken
     *
     * @return mixed
     */
    public function getThreeDSecureVaultNonce($paymentMethodToken)
    {
        $this->init();

        $result = Braintree_PaymentMethodNonce::create($paymentMethodToken);
        return $result->paymentMethodNonce->nonce;
    }

    /**
     * Try and load the Braintree customer from the stored customer ID
     *
     * @param $braintreeCustomerId
     *
     * @return Braintree_Customer
     */
    public function getCustomer($braintreeCustomerId)
    {
        // Try and load it from the customer
        if(!$this->customer && !isset($this->customer[$braintreeCustomerId])) {
            try {
                $this->customer[$braintreeCustomerId] = Braintree_Customer::find($braintreeCustomerId);
            } catch (Exception $e) {
                return false;
            }
        }

        return $this->customer[$braintreeCustomerId];
    }

    /**
     * Check to see whether this customer already exists
     *
     * @return bool|object
     */
    public function checkIsCustomer()
    {
        try {
            // Check to see that we can generate a braintree ID
            if($braintreeId = $this->getBraintreeId()) {

                // Proxy this request to the other method which has caching
                return $this->getCustomer($braintreeId);
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Generate a server side token with the specified account ID
     *
     * @return mixed
     */
    public function generateToken()
    {
        // Use the class to generate the token
        return Braintree_ClientToken::generate(
            array("merchantAccountId" => $this->getMerchantAccountId())
        );
    }


    /**
     * Check a customer owns the method we're trying to modify
     *
     * @param $paymentMethod
     *
     * @return bool
     */
    public function customerOwnsMethod($paymentMethod)
    {
        // Grab the customer ID from the customers account
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getBraintreeCustomerId();

        // Detect which type of payment method we've got here
        if($paymentMethod instanceof Braintree_PayPalAccount) {

            // Grab the customer
            $customer = $this->getCustomer($customerId);

            // Store all the tokens in an array
            $customerTokens = array();

            // Check the customer has PayPal Accounts
            if(isset($customer->paypalAccounts)) {

                /* @var $payPalAccount Braintree_PayPalAccount */
                foreach($customer->paypalAccounts as $payPalAccount) {
                    if(isset($payPalAccount->token)) {
                        $customerTokens[] = $payPalAccount->token;
                    }
                }
            } else {
                return false;
            }

            // Check to see if this customer account contains this token
            if(in_array($paymentMethod->token, $customerTokens)) {
                return true;
            }

            return false;

        } else if(isset($paymentMethod->customerId) && $paymentMethod->customerId == $customerId) {

            return true;
        }

        return false;
    }

    /**
     * Retrieve the Braintree ID from Magento
     *
     * @return bool|string
     */
    protected function getBraintreeId()
    {
        // Some basic caching
        if(!$this->braintreeId) {

            // Is the customer already logged in
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {

                // Retrieve the current customer
                $customer = Mage::getSingleton('customer/session')->getCustomer();

                // Determine whether they have a braintree customer ID already
                if ($brainteeId = $customer->getBraintreeCustomerId()) {
                    $this->braintreeId = $customer->getBraintreeCustomerId();
                } else {
                    // If not let's create them one
                    $this->braintreeId = $this->buildCustomerId();
                    $customer->setBraintreeCustomerId($this->braintreeId)->save();
                }

            } else {
                if ((Mage::getSingleton('checkout/type_onepage')->getCheckoutMethod() == 'login_in' || Mage::getSingleton('checkout/type_onepage')->getCheckoutMethod() == Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER)) {

                    // Check to see if we've already generated an ID
                    if($braintreeId = Mage::getSingleton('checkout/session')->getBraintreeCustomerId()) {
                        $this->braintreeId = $braintreeId;
                    } else {
                        // If the user plans to register let's build them an ID and store it in their session
                        $this->braintreeId = $this->buildCustomerId();
                        Mage::getSingleton('checkout/session')->setBraintreeCustomerId($this->braintreeId);
                    }
                }
            }

        }

        return $this->braintreeId;
    }

    /**
     * Return the admin config value
     **/
    protected function getAdminConfigValue($path)
    {
        // If we have the getConfigDataValue use that
        if(method_exists('Mage_Adminhtml_Model_Config_Data','getConfigDataValue')) {
            return Mage::getSingleton('adminhtml/config_data')->getConfigDataValue($path);
        }

        // Otherwise use the default amazing getStoreConfig
        return Mage::getStoreConfig($path);
    }

    /**
     * If a transaction has been voided it's transaction ID can change
     *
     * @param $transactionId
     *
     * @return string
     */
    public function getCleanTransactionId($transactionId)
    {
        return strtok($transactionId, '-');
    }

    /**
     * Validate the credentials within the admin area
     *
     * @return bool
     */
    public function validateCredentials($prettyResponse = false, $alreadyInit = false, $merchantAccountId = false, $throwException = false)
    {
        // Try to init the environment
        try {
            if(!$alreadyInit) {

                // If we're within the admin we want to grab these values from whichever store we're modifying
                if(Mage::app()->getStore()->isAdmin()) {
                    Braintree_Configuration::environment($this->getAdminConfigValue(self::BRAINTREE_ENVIRONMENT_PATH));
                    Braintree_Configuration::merchantId($this->getAdminConfigValue(self::BRAINTREE_MERCHANT_ID_PATH));
                    Braintree_Configuration::publicKey($this->getAdminConfigValue(self::BRAINTREE_PUBLIC_KEY_PATH));
                    Braintree_Configuration::privateKey($this->getAdminConfigValue(self::BRAINTREE_PRIVATE_KEY_PATH));
                } else {
                    $this->init();
                }
            }

            // Attempt to retrieve the gateway plans to check
            Braintree_Configuration::gateway()->plan()->all();

        } catch (Exception $e) {

            // Do we want to rethrow the exception?
            if($throwException) {
                throw $e;
            }

            // Otherwise give the user a little bit more information
            if($prettyResponse) {
                return '<span style="color: red;font-weight: bold;" id="braintree-valid-config">' . Mage::helper('gene_braintree')->__('Invalid Credentials') . '</span><br />' . Mage::helper('gene_braintree')->__('Payments cannot be processed until this is resolved, due to this the methods will be hidden within the checkout');
            }

            // Otherwise return with a boolean
            return false;
        }

        // Check to see if we've been passed the merchant account ID?
        if(!$merchantAccountId) {
            if(Mage::app()->getStore()->isAdmin()) {
                $merchantAccountId = $this->getAdminConfigValue(self::BRAINTREE_MERCHANT_ACCOUNT_ID_PATH);
            } else {
                $merchantAccountId = $this->getMerchantAccountId();
            }
        }

        // Validate the merchant account ID
        try {
            Braintree_Configuration::gateway()->merchantAccount()->find($merchantAccountId);
        } catch (Exception $e) {

            // Do we want to rethrow the exception?
            if($throwException) {
                throw $e;
            }

            // Otherwise do we want a pretty response?
            if($prettyResponse) {
                return '<span style="color: orange;font-weight: bold;" id="braintree-valid-config">' . Mage::helper('gene_braintree')->__('Invalid Merchant Account ID') . '</span><br />' . Mage::helper('gene_braintree')->__('Payments cannot be processed until this is resolved. We cannot find your merchant account ID associated with the other credentials you\'ve provided, please update this field');
            }

            // Finally return a boolean
            return false;
        }

        if($prettyResponse) {
            return '<span style="color: green;font-weight: bold;" id="braintree-valid-config">' . Mage::helper('gene_braintree')->__('Valid Credentials') . '</span><br />' . Mage::helper('gene_braintree')->__('You\'re ready to accept payments via Braintree');
        }
        return true;
    }

    /**
     * Validate the credentials once, this is used during the payment methods available check
     * @return bool
     */
    public function validateCredentialsOnce()
    {
        // Check to see if it's been validated yet
        if(is_null($this->validated)) {

            // Check the Braintree lib version is above 2.32, as this is when 3D secure appeared
            if (Braintree_Version::get() < 2.32) {
                $this->validated = false;
            } else {

                // Check that the module is fully setup
                if (!Mage::getStoreConfig(Gene_Braintree_Model_Wrapper_Braintree::BRAINTREE_ENVIRONMENT_PATH)
                    || !Mage::getStoreConfig(Gene_Braintree_Model_Wrapper_Braintree::BRAINTREE_MERCHANT_ID_PATH)
                    || !Mage::getStoreConfig(Gene_Braintree_Model_Wrapper_Braintree::BRAINTREE_PUBLIC_KEY_PATH)
                    || !Mage::getStoreConfig(Gene_Braintree_Model_Wrapper_Braintree::BRAINTREE_PRIVATE_KEY_PATH)
                ) {
                    // If not the payment methods aren't available
                    $this->validated = false;

                } else {

                    // Attempt to validate credentials
                    try {

                        // Passing true will cause the system to rethrow exceptions
                        if(Mage::getModel('gene_braintree/wrapper_braintree')->validateCredentials(false, false, false, true)) {

                            // Mark our flag as true
                            $this->validated = true;

                        } else {

                            // Mark our flag as false, this shouldn't even return false it should always throw an
                            // Exception but just in case
                            $this->validated = false;
                        }

                    } catch (Exception $e) {

                        // If it fails log it
                        Gene_Braintree_Model_Debug::log('CRITICAL ERROR: The system was unable to connect to Braintree, error is below');
                        Gene_Braintree_Model_Debug::log($e);

                        // If the validateCredentials throws an exception it has failed
                        $this->validated = false;
                    }

                }
            }
        }

        return $this->validated;
    }

    /**
     * Build up the sale request
     *
     * @param $amount
     * @param array $paymentDataArray
     * @param Mage_Sales_Model_Order $order
     * @param bool $submitForSettlement
     * @param bool $deviceData
     * @param bool $storeInVault
     * @param bool $threeDSecure
     * @param array $extra
     *
     * @return array
     *
     * @throws Mage_Core_Exception
     */
    public function buildSale(
        $amount,
        array $paymentDataArray,
        Mage_Sales_Model_Order $order,
        $submitForSettlement = true,
        $deviceData = false,
        $storeInVault = false,
        $threeDSecure = false,
        $extra = array()
    ) {
        // Check we always have an ID
        if (!$order->getIncrementId()) {
            Mage::throwException('Your order has become invalid, please try refreshing.');
        }

        // Store whether or not we created a new method
        $createdMethod = false;

        // If the user is already a customer and wants to store in the vault we've gotta do something a bit special
        if($storeInVault && $this->checkIsCustomer() && isset($paymentDataArray['paymentMethodNonce'])) {

            // Create the payment method with this data
            $paymentMethodCreate = array(
                'customerId' => $this->getBraintreeId(),
                'paymentMethodNonce' => $paymentDataArray['paymentMethodNonce'],
                'billingAddress' => $this->buildAddress($order->getBillingAddress())
            );

            // Log the create array
            Gene_Braintree_Model_Debug::log(array('Braintree_PaymentMethod' => $paymentMethodCreate));

            // Create a new billing method
            $result = Braintree_PaymentMethod::create($paymentMethodCreate);

            // Log the response from Braintree
            Gene_Braintree_Model_Debug::log(array('Braintree_PaymentMethod:result' => $paymentMethodCreate));

            // Verify the storing of the card was a success
            if(isset($result->success) && $result->success == true) {

                /* @var $paymentMethod Braintree_CreditCard */
                $paymentMethod = $result->paymentMethod;

                // Check to see if the token is set
                if(isset($paymentMethod->token) && !empty($paymentMethod->token)) {

                    // We no longer need this nonce
                    unset($paymentDataArray['paymentMethodNonce']);

                    // Instead use the token
                    $paymentDataArray['paymentMethodToken'] = $paymentMethod->token;

                    // Create a flag for other methods
                    $createdMethod = true;
                }

            } else {
                Mage::throwException($result->message . Mage::helper('gene_braintree')->__(' Please try again or attempt refreshing the page.'));
            }
        }

        // Build up the initial request parameters
        $request = array(
            'amount'             => $amount,
            'orderId'            => $order->getIncrementId(),
            'merchantAccountId'  => $this->getMerchantAccountId($order),
            'channel'            => 'MagentoVZero',
            'options'            => array(
                'submitForSettlement' => $submitForSettlement,
                'storeInVault'        => $storeInVault
            )
        );

        // Input the allowed payment method info
        $allowedPaymentInfo = array('paymentMethodNonce','paymentMethodToken','token','cvv');
        foreach($paymentDataArray as $key => $value) {
            if(in_array($key, $allowedPaymentInfo)) {
                if($key == 'cvv') {
                    $request['creditCard']['cvv'] = $value;
                } else {
                    $request[$key] = $value;
                }
            } else {
                Mage::throwException($key.' is not allowed within $paymentDataArray');
            }
        }

        // Include the customer if we're creating a new one
        if(!$this->checkIsCustomer() && (Mage::getSingleton('customer/session')->isLoggedIn() ||
                (Mage::getSingleton('checkout/type_onepage')->getCheckoutMethod() == 'login_in' || Mage::getSingleton('checkout/type_onepage')->getCheckoutMethod() == Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER))) {
            $request['customer'] = $this->buildCustomer($order);
        } else {
            // If the customer exists but we aren't using the vault we want to pass a customer object with no ID
            $request['customer'] = $this->buildCustomer($order, false);
        }

        // Do we have any deviceData to send over?
        if ($deviceData) {
            $request['deviceData'] = $deviceData;
        }

        // Include the shipping address
        if ($order->getShippingAddress()) {
            $request['shipping'] = $this->buildAddress($order->getShippingAddress());
        }

        // Include the billing address
        if ($order->getBillingAddress()) {
            $request['billing'] = $this->buildAddress($order->getBillingAddress());
        }

        // Is 3D secure enabled?
        if($threeDSecure !== false && !$createdMethod) {
            $request['options']['three_d_secure']['required'] = true;
        }

        // Any extra information we want to supply
        if(!empty($extra) && is_array($extra)) {
            $request = array_merge($request, $extra);
        }

        return $request;
    }

    /**
     * Attempt to make the sale
     *
     * @param $saleArray
     *
     * @return stdClass
     */
    public function makeSale($saleArray)
    {
        // Call the braintree library
        return Braintree_Transaction::sale(
            $saleArray
        );
    }

    /**
     * Submit a payment for settlement
     *
     * @param $transactionId
     * @param $amount
     *
     * @throws Mage_Core_Exception
     */
    public function submitForSettlement($transactionId, $amount)
    {
        // Attempt to submit for settlement
        $result = Braintree_Transaction::submitForSettlement($transactionId, $amount);

        return $result;
    }

    /**
     * Build the customers ID, md5 a uniquid
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    private function buildCustomerId()
    {
        return md5(uniqid('braintree_',true));
    }

    /**
     * Build a Magento address model into a Braintree array
     *
     * @param Mage_Sales_Model_Order_Address $address
     *
     * @return array
     */
    private function buildAddress(Mage_Sales_Model_Order_Address $address)
    {
        // Build up the initial array
        $return = array(
            'firstName'         => $address->getFirstname(),
            'lastName'          => $address->getLastname(),
            'streetAddress'     => $address->getStreet1(),
            'locality'          => $address->getCity(),
            'postalCode'        => $address->getPostcode(),
            'countryCodeAlpha2' => $address->getCountry()
        );

        // Any extended address?
        if ($address->getStreet2()) {
            $return['extendedAddress'] = $address->getStreet2();
        }

        // Region
        if ($address->getRegion()) {
            $return['region'] = $address->getRegionCode();
        }

        // Check to see if we have a company
        if ($address->getCompany()) {
            $return['company'] = $address->getCompany();
        }

        return $return;
    }

    /**
     * Return the correct merchant account ID
     *
     * @return mixed
     */
    public function getMerchantAccountId(Mage_Sales_Model_Order $order = null)
    {
        // If multi-currency is enabled use the mapped merchant account ID
        if($currencyCode = $this->hasMappedCurrencyCode($order)) {

            // Return the mapped currency code
            return $currencyCode;
        }

        // Otherwise return the one from the store
        return Mage::getStoreConfig(self::BRAINTREE_MERCHANT_ACCOUNT_ID_PATH, ($order ? $order->getStoreId() : null));
    }

    /**
     * If we have a mapped currency code return it
     *
     * @return bool
     */
    public function hasMappedCurrencyCode(Mage_Sales_Model_Order $order = null)
    {
        // If multi-currency is enabled use the mapped merchant account ID
        if($this->currencyMappingEnabled($order)) {

            // Retrieve the mapping from the config
            $mapping = Mage::helper('core')->jsonDecode(Mage::getStoreConfig(self::BRAINTREE_MULTI_CURRENCY_MAPPING, ($order ? $order->getStoreId() : false)));

            // Verify it decoded correctly
            if(is_array($mapping) && !empty($mapping)) {

                // If we haven't been given an order use the quote currency code
                $currency = (!$order ? $this->getQuote()->getQuoteCurrencyCode() : $order->getOrderCurrencyCode());

                // Verify we have a mapping value for this currency
                if(isset($mapping[$currency]) && !empty($mapping[$currency])) {

                    // These should never have spaces in so make sure we trim it
                    return trim($mapping[$currency]);
                }
            }
        }

        return false;
    }

    /**
     * Do we have currency mapping enabled?
     *
     * @return bool
     */
    public function currencyMappingEnabled(Mage_Sales_Model_Order $order = null)
    {
        return Mage::getStoreConfigFlag(self::BRAINTREE_MULTI_CURRENCY)
            && Mage::getStoreConfig(self::BRAINTREE_MULTI_CURRENCY_MAPPING)
            && ((!$order ? $this->getQuote()->getQuoteCurrencyCode() : $order->getOrderCurrencyCode())
                != (!$order ? $this->getQuote()->getBaseCurrencyCode() : $order->getBaseCurrencyCode()));
    }

    /**
     * Get the current quote
     *
     * @return \Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        // If we're within the admin return the admin quote
        if(Mage::app()->getStore()->isAdmin()) {
            return Mage::getSingleton('adminhtml/session_quote')->getQuote();
        }

        return Mage::helper('checkout')->getQuote();
    }

    /**
     * If we have a mapped currency code we need to convert the currency
     *
     * @param \Mage_Sales_Model_Order $order
     * @param                         $amount
     *
     * @return string
     * @throws \Zend_Currency_Exception
     */
    public function getCaptureAmount(Mage_Sales_Model_Order $order = null, $amount)
    {
        // If we've got a mapped currency code the amount is going to change
        if($this->hasMappedCurrencyCode($order)) {

            // If we don't have an order yet get the quote capture amount
            if($order === null) {
                return $this->convertCaptureAmount($this->getQuote()->getBaseCurrencyCode(), $this->getQuote()->getQuoteCurrencyCode(), $amount);
            }

            // Convert the capture amount
            return $this->convertCaptureAmount($order->getBaseCurrencyCode(), $order->getOrderCurrencyCode(), $amount);
        }

        // Always make sure the number has two decimal places
        return Mage::helper('gene_braintree')->formatPrice($amount);
    }

    /**
     * @param $amount
     *
     * @return string
     * @throws \Zend_Currency_Exception
     */
    public function convertCaptureAmount($baseCurrencyCode, $orderQuoteCurrencyCode, $amount)
    {
        // Convert the current
        $convertedCurrency = Mage::helper('directory')->currencyConvert($amount, $baseCurrencyCode, $orderQuoteCurrencyCode);

        // Format it to a precision of 2
        $options = array(
            'currency' => $orderQuoteCurrencyCode,
            'display' => ''
        );

        return Mage::app()->getLocale()->currency($orderQuoteCurrencyCode)->toCurrency($convertedCurrency, $options);
    }

    /**
     * Build up the customers data onto an object
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return array
     */
    private function buildCustomer(Mage_Sales_Model_Order $order, $includeId = true)
    {
        $customer = array(
            'firstName' => $order->getCustomerFirstname(),
            'lastName'  => $order->getCustomerLastname(),
            'email'     => $order->getCustomerEmail(),
            'phone'     => $order->getBillingAddress()->getTelephone()
        );

        // Shall we include the customer ID?
        if($includeId) {
            $customer['id'] = $this->getBraintreeId();
        }

        // Handle empty data with alternatives
        if(empty($customer['firstName'])) {
            $customer['firstName'] = $order->getBillingAddress()->getFirstname();
        }
        if(empty($customer['lastName'])) {
            $customer['lastName'] = $order->getBillingAddress()->getLastname();
        }
        if(empty($customer['email'])) {
            $customer['email'] = $order->getBillingAddress()->getEmail();
        }

        return $customer;
    }

    /**
     * Clone a transaction
     *
     * @param $transactionId
     * @param $amount
     *
     * @return bool|mixed
     */
    public function cloneTransaction($transactionId, $amount, $submitForSettlement = true)
    {
        // Attempt to clone the transaction
        try {
            $result = Braintree_Transaction::cloneTransaction($transactionId, array(
                'amount'  => $amount,
                'options' => array(
                    'submitForSettlement' => $submitForSettlement
                )
            ));

            return $result;

        } catch (Exception $e) {

            // Log the issue
            Gene_Braintree_Model_Debug::log(array('cloneTransaction' => $e));

            return false;
        }
    }

    /**
     * Parse Braintree errors as a string
     *
     * @param $braintreeErrors
     *
     * @return string
     */
    public function parseErrors($braintreeErrors)
    {
        $errors = array();
        foreach($braintreeErrors as $error) {
            $errors[] = $error->code . ': ' . $error->message;
        }

        return implode(', ', $errors);
    }

}