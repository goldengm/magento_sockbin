<?php

require_once Mage::getModuleDir('controllers', 'Apptha_Sociallogin') . DS . 'IndexController.php';

/**
 * Class Snowdog_Sociallogin_IndexController
 */
class Snowdog_Sociallogin_IndexController
    extends Apptha_Sociallogin_IndexController
{

    /**
     * @facebook login action
     *
     * Connect facebook Using oAuth coonection.
     *
     * @return string redirect URL
     *
     */
    public function fbloginAction()
    {
        require 'sociallogin/facebook/facebook.php';
        require 'sociallogin/config/fbconfig.php';
        /**
         * create facebook object using @APP_ID, @APP_SECRET
         */
        $facebook = new Slogin_Facebook (array(
            'appId' => APP_ID,
            'secret' => APP_SECRET,
            'cookie' => false
        ));

        /**
         * Retrieve user information from @facebook
         */
        $user = $facebook->getUser();

        if ($user) {
            try {

                /**
                 * Proceed the further action for customer who authenticated from @facebook
                 */
                $userProfile = $facebook->api('/me?fields=id,name,email');
                $name = explode(' ', $userProfile['name']);
                $firstName = $name[0];
                $email = $userProfile ['email'];
                $lastName = isset($name[1]) ? $name[1] : 'ToBeSet';
                $data = $this->getRequest()->getParam('fb');
                if ($email == '') {
                    Mage::getSingleton('customer/session')->addError($this->__('Facebook Login connection failed'));
                    $url = Mage::helper('customer')->getAccountUrl();
                    return $this->_redirectUrl($url);
                } else {
                    $this->customerAction($firstName, $lastName, $email, 'Facebook', $data);
                }
            } catch (SloginFacebookApiException $e) {

                Mage::log($e);
                $user = null;
                Mage::getSingleton('customer/session')->addError($e);
                $url = Mage::helper('customer')->getAccountUrl();
                Mage::getSingleton('customer/session')->clear();
                $this->_redirectUrl($url);
            }
        }
    }

    /**
     * Customer Register Action
     *
     * @return string
     */
    public function customerAction($firstname, $lastname, $email, $provider, $data)
    {
        $customer = Mage::getModel('customer/customer');
        $collection = $customer->getCollection();
        $adminApproval = Mage::getStoreConfig('sociallogin/general/need_approval');
        $groupId = 1;
        if ($customer->getSharingConfig()->isWebsiteScope()) {
            $collection->addAttributeToFilter('website_id', Mage::app()->getWebsite()->getId());
        }
        if ($this->_getCustomerSession()->isLoggedIn()) {
            $collection->addFieldToFilter('entity_id', array(
                'neq' => $this->_getCustomerSession()->getCustomerId()
            ));
        }
        /**
         * Retrieves the customer details depends on @email
         */
        $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email);
        $customerIdByEmail = $customer->getId();
        if ($customerIdByEmail == '') {
            $standardInfo ['email'] = $email;
        } else {
            $standardInfo ['email'] = $email;
        }
        /**
         * Retrieving the customer form posted values.
         *
         * @param array $standardInfo
         *            array values such as@first_name,@last_name and @email
         */
        $standardInfo ['first_name'] = $firstname;
        $standardInfo ['last_name'] = $lastname;
        $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($standardInfo ['email']);
        /**
         * Check if Already registered customer.
         */
        if ($customer->getId()) {
            $customerGroupId = $customer->getGroupId();
            $loggedCustomerStatus = $customer->getCustomerstatus();
            if ($loggedCustomerStatus == 1) {
                $this->_getCustomerSession()->setCustomerAsLoggedIn($customer);
                $this->_getCustomerSession()->addSuccess($this->__('Your account has been successfully connected through' . ' ' . $provider));
            }
            if ($customerGroupId == $groupId && ($loggedCustomerStatus == 0 || $loggedCustomerStatus == 2)) {
                $this->_getCustomerSession()->setCustomerAsLoggedIn($customer);
                Mage::getSingleton('core/session')->addSuccess($this->__('Admin Approval is required. Please wait until admin confirms your Seller Account'));
            }

            /**
             * Get customer current URL from customer session.
             */
            $link = Mage::getSingleton('customer/session')->getLink();

            if (!empty ($link)) {
                $requestPath = trim($link, '/');
            }
            /**
             * Check if customer current URL is checkout URL.
             */
            if ($requestPath == 'checkout/onestep') {
                $this->_redirect($requestPath);
                return;
            } else {
                $enableRedirectStatus = Mage::getStoreConfig('sociallogin/general/enable_redirect');
                if ($enableRedirectStatus) {
                    if ($customerGroupId == $groupId && $loggedCustomerStatus == 1) {
                        $redirect = Mage::getUrl('marketplace/seller/dashboard');
                    } else {
                        $redirect = $this->_loginPostRedirect();
                    }
                } else {
                    $redirect = Mage::getSingleton('core/session')->getReLink();
                }
                $this->_redirectUrl($redirect);
                return;
            }
        }
        /**
         * Generate Random Password .
         * Set Login provider if customer uses social networks such as @google, @yahoo, @facebook and @twitter.
         */
        $randomPassword = $customer->generatePassword(8);
        /**
         * Set Login provider if customer uses social networks such as @google, @yahoo, @facebook and @twitter.
         */
        $customer->setId(null)->setSkipConfirmationIfEmail($standardInfo ['email'])->setFirstname($standardInfo ['first_name'])->setLastname($standardInfo ['last_name'])->setEmail($standardInfo ['email'])->setPassword($randomPassword)->setConfirmation($randomPassword)->setLoginProvider($provider);
        /**
         * Checking admin approval is required for seller registration or not
         */
        if ($adminApproval == 1 && $data == 1) {
            $customer->setGroupId($groupId);
            $customer->setCustomerstatus('0');
        } elseif ($adminApproval != 1 && $data == 1) {
            $customer->setGroupId($groupId);
            $customer->setCustomerstatus('1');
        } else {
            $customer->setCustomerstatus('1');
        }
        if ($this->getRequest()->getParam('is_subscribed', false)) {
            $customer->setIsSubscribed(1);
        }
        /**
         * Registration will fail if tax required, also if @DOB, @Gender aren't allowed in your profile
         */
        $errors = array();
        $validationCustomer = $customer->validate();
        if (is_array($validationCustomer)) {
            $errors = array_merge($validationCustomer, $errors);
        }
        $validationResult = true;
        $this->_getCustomerSession();
        if (true === $validationResult) {
            $customerId = $customer->save()->getId();
            $this->getStatus($customerId, $customer, $adminApproval, $data);
        } else {
            $this->_getCustomerSession()->setCustomerFormData($customer->getData());
            $this->_getCustomerSession()->addError($this->__('User profile can\'t provide all required info, please register and then connect with Apptha Social login.'));
            if (is_array($errors)) {
                foreach ($errors as $errorMessage) {
                    $this->_getCustomerSession()->addError($errorMessage);
                }
            }
            $this->_redirect('customer/account/create');
            return;
        }
    }

    /**
     * Customer Register Action
     *
     * validate the social regiter form posted values
     *
     * @return string Redirect URL.
     */
    public function createPostAction()
    {
        $adminApproval = Mage::getStoreConfig('marketplace/admin_approval_seller_registration/need_approval');
        $sellerRegisteration = 0;
        $customer = Mage::getModel('customer/customer');
        $session = $this->_getCustomerSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }

        $enableCaptcha = Mage::getStoreConfig('customer/captcha/enable');

        if ($enableCaptcha == '1') {
            $newCaptch = $this->getRequest()->getPost('captcha');
            $captcha = Mage::getModel('customer/session')->getData('user_create_word');
            $captchaImgData = $captcha ['data'];
            if ($newCaptch ['user_create'] != $captchaImgData) {
                $this->getResponse()->setBody($this->__('Incorrect CAPTCHA.'));
                return;
            }
        }

        $session->setEscapeMessages(true);
        if ($this->getRequest()->isPost()) {
            $errors = array();
            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

//            $groupId = Mage::helper ( 'marketplace' )->getGroupId ();
//            if ($adminApproval == 1 && $sellerRegisteration == 1) {
//                $customer->setGroupId ( $groupId );
//                $customer->setCustomerstatus ( '0' );
//            } elseif ($adminApproval != 1 && $sellerRegisteration == 1) {
//                $customer->setGroupId ( $groupId );
//                $customer->setCustomerstatus ( '1' );
//            } else {
//                $customer->setCustomerstatus ( '1' );
//            }

            //set customer group to General
            $customer->setGroupId(1);
            $customer->setCustomerstatus('1');

            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_create')->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());

            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            $customer->getGroupId();
            if ($this->getRequest()->getPost('create_address')) {

                $address = Mage::getModel('customer/address');

                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('customer_register_address')->setEntity($address);

                $addressData = $addressForm->extractData($this->getRequest(), 'address', false);
                $addressErrors = $addressForm->validateData($addressData);
                if ($addressErrors === true) {
                    $address->setId(null)->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
                    $addressForm->compactData($addressData);
                    $customer->addAddress($address);

                    $addressErrors = $address->validate();
                    if (is_array($addressErrors)) {
                        $errors = array_merge($errors, $addressErrors);
                    }
                } else {
                    $errors = array_merge($errors, $addressErrors);
                }
            }
            try {
                $customerErrors = $customerForm->validateData($customerData);

                if ($customerErrors !== true) {
                    $errors = array_merge($customerErrors, $errors);
                } else {
                    $customerForm->compactData($customerData);

                    $customer->setPassword($this->getRequest()->getPost('password'));

                    $magentoVersion = Mage::getVersion();
                    if (version_compare($magentoVersion, '1.9.1', '>=')) {
                        $customer->setPasswordConfirmation($this->getRequest()->getPost('confirmation'));
                    } else {
                        $customer->setConfirmation($this->getRequest()->getPost('confirmation'));
                    }

                    $customerErrors = $customer->validate();
                    if (is_array($customerErrors)) {
                        $errors = array_merge($customerErrors, $errors);
                    }
                }
                $validationResult = count($errors) == 0;
                if (true === $validationResult) {
                    $customerId = $customer->save()->getId();

                    if ($customer->isConfirmationRequired() && $adminApproval == 1 && $sellerRegisteration == 1) {
                        $customer->sendNewAccountEmail('confirmation', $session->getBeforeAuthUrl(), Mage::app()->getStore()->getId());
                        Mage::getModel('marketplace/sellerprofile')->adminApproval($customerId);
                        $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
                        $this->getResponse()->setBody(Mage::getUrl('/index', array(
                            '_secure' => true
                        )));
                        return;
                    }
                    if ($customer->isConfirmationRequired()) {
                        $customer->sendNewAccountEmail('confirmation', $session->getBeforeAuthUrl(), Mage::app()->getStore()->getId());
                        $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
                        $this->getResponse()->setBody(Mage::getUrl('/index', array(
                            '_secure' => true
                        )));
                        return;
                    }
                    if ($adminApproval == 1 && $sellerRegisteration == 1) {
                        Mage::getModel('marketplace/sellerprofile')->adminApproval($customerId);
                        Mage::dispatchEvent('customer_register_success', array(
                            'account_controller' => $this,
                            'customer' => $customer
                        ));
                        Mage::getSingleton('core/session')->addSuccess($this->__('Admin Approval is required. Please wait until admin confirms your Seller Account'));
                        $this->getResponse()->setBody(Mage::getUrl('/index', array(
                            '_secure' => true
                        )));
                        return;
                    } else {
                        if ($sellerRegisteration == 1) {
                            Mage::getModel('marketplace/sellerprofile')->newSeller($customerId);
                        }
                        Mage::dispatchEvent('customer_register_success', array(
                            'account_controller' => $this,
                            'customer' => $customer
                        ));
                        $session->setCustomerAsLoggedIn($customer);
                        $session->renewSession();
                        $this->getResponse()->setBody($this->_welcomeCustomer($customer));
                    }
                } else {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->$errorMessage;
                        }
                        $this->getResponse()->setBody($errorMessage);
                        return;
                    } else {
                        $session->addError($this->__('Invalid customer data'));
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost());
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $message = $this->__('Email already exists');
                    $this->getResponse()->setBody($message);
                    $session->setEscapeMessages(false);
                    return;
                } else {
                    $message = $e->getMessage();
                    $this->getResponse()->setBody($message);
                    return;
                }
                $session->addError($message);
            } catch (Exception $e) {

                $session->setCustomerFormData($this->getRequest()->getPost())->addException($e, $this->__('Cannot save the customer.'));
            }
        }
        if (!empty ($message)) {
            $this->getResponse()->setBody($message);
        }

        if (!Mage::getStoreConfigFlag('customer/startup/redirect_dashboard')) {
            $this->getResponse()->setBody(Mage::getUrl('/index', array(
                '_secure' => true
            )));
        } else {
            $this->getResponse()->setBody(Mage::helper('customer')->getAccountUrl());
        }
    }

    /**
     * Customer Create Account layout render Action
     *
     * Rendering the layout if social login extension is enabled
     */
    public function createAction() {
        if ($this->_getCustomerSession ()->isLoggedIn ()) {
            $this->_redirect ( '*/*/' );
            return;
        } else {
            $enableStatus = Mage::getStoreConfig ( 'sociallogin/general/enable_sociallogin' );
            if ($enableStatus != 1) {
                return;
            }
        }

        $this->loadLayout ();
        $this->_initLayoutMessages ( 'customer/session' );
        $this->renderLayout ();
    }

    /**
     * Get status of current customer
     *
     * @param $customerId
     * @param $customer
     * @param $adminApproval
     * @param $data
     */
    public function getStatus($customerId, $customer, $adminApproval, $data)
    {
        if ($adminApproval == 1 && $data == 1) {
            Mage::getModel('marketplace/sellerprofile')->adminApproval($customerId);
            Mage::dispatchEvent('customer_register_success', array(
                'account_controller' => $this,
                'customer' => $customer
            ));
            Mage::getSingleton('core/session')->addSuccess($this->__('Admin Approval is required. Please wait until admin confirms your Seller Account'));
            $redirecturl = $this->getResponse()->setBody(Mage::getUrl('/index', array(
                '_secure' => true
            )));
            $this->_redirectUrl($redirecturl);
            return;
        } else {
            $session = $this->_getCustomerSession();
            Mage::dispatchEvent('customer_register_success', array(
                'account_controller' => $this,
                'customer' => $customer
            ));
            $session->setCustomerAsLoggedIn($customer);
            $session->renewSession();
            $redirecturl = $this->getResponse()->setBody($this->_welcomeCustomer($customer));
            $this->_redirectUrl($redirecturl);
            $this->_getCustomerSession()->addSuccess($this->__('Thank you for registering with %s', Mage::app()->getStore()->getFrontendName()) . '. ' . $this->__('You will receive welcome email with registration info in a moment.'));
            $customer->sendNewAccountEmail();

            $this->_getCustomerSession()->setCustomerAsLoggedIn($customer);
            $link = Mage::getSingleton('customer/session')->getLink();
            if (!empty ($link)) {
                $requestPath = trim($link, '/');
            }
            if ($requestPath == 'checkout/onestep') {
                $this->_redirect($requestPath);
                return;
            } else {
                $enableRedirectStatus = Mage::getStoreConfig('sociallogin/general/enable_redirect');
                if ($enableRedirectStatus) {
                    $redirect = $this->_loginPostRedirect();
                } else {
                    $redirect = Mage::getSingleton('core/session')->getReLink();
                }
                $this->_redirectUrl($redirect);
                return;
            }
        }
    }

    /**
     * Retrieve customer session from core customer session
     *
     * @return array
     */
    private function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

}
				