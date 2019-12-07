<?php

require_once(Mage::getModuleDir('controllers','TM_FireCheckout').DS.'IndexController.php');

class Snowdog_Pobox_Firecheckout_IndexController extends TM_FireCheckout_IndexController
{

    public function saveOrderAction()
    {
        if (version_compare(Mage::helper('firecheckout')->getMagentoVersion(), '1.8.0.0') >= 0) {
            if (!$this->_validateFormKey()) {
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
                    'success' => false,
                    'error'   => true,
                    'error_messages' => $this->__('Invalid Form Key. Please refresh the page.')
                )));
                return;
            }
        }

        if ($this->_expireAjax()) {
            return;
        }

        // sage server fix
//        $sagepayModel = Mage::getModel('sagepayserver2/sagePayServer_session');
//        if ($sagepayModel) {
//            $sessId = Mage::getModel('core/session')->getSessionId();
//            $_s = $sagepayModel->loadBySessionId($sessId);
//            if ($_s->getId()) {
//                $_s->delete();
//            }
//        }
        // sage server fix

        $result = array();
        /* @var TM_FireCheckout_Model_Type_Standard */
        $checkout = $this->getCheckout();
        /* @var Mage_Sales_Model_Quote */
        $quote = $checkout->getQuote();

        try {
            $this->_saveAdvoxInpost();
            $checkout->applyShippingMethod($this->getRequest()->getPost('shipping_method', false));
            $deliveryDate = $this->getRequest()->getPost('delivery_date');
            if ($deliveryDate) {
                $result = $checkout->saveDeliveryDate($deliveryDate);
                if (is_array($result)) {
                    $result['success'] = false;
                    $result['error']   = true;
                    $result['error_messages'] = $result['message'];
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
            }

            $quote->setFirecheckoutCustomerComment($this->getRequest()->getPost('order-comment'));

            $billing = $this->getRequest()->getPost('shipping', array());
            if (!isset($billing['lastname']) || !$billing['lastname']) {
                $billing['lastname'] = '*';
            }
            if (!isset($billing['telephone']) || !$billing['telephone']) {
                $billing['telephone'] = '*';
            }

            $shipping = $this->getRequest()->getPost('billing', array());
            if (!isset($shipping['lastname']) || !$shipping['lastname']) {
                $shipping['lastname'] = '*';
            }
            if (!isset($shipping['telephone']) || !$shipping['telephone']) {
                $shipping['telephone'] = '*';
            }

            if(isset($billing['same_as_billing'])){
                $billing = $shipping;
            }else{
                //fix empty email in change place billing and shippng address
               $billing['email'] = $shipping['email'];
            }

            $result = $checkout->saveBilling(
                $billing,
                $this->getRequest()->getPost('billing_address_id', false)
            );

            if ($result) {
                $result['success'] = false;
                $result['error']   = true;
                if ($result['message'] === $checkout->getCustomerEmailExistsMessage()) {
                    unset($result['message']);
                    $result['body'] = array(
                        'id'      => 'emailexists',
                        'modal'   => 1,
                        'window'  => array(
                            'triggers' => array(),
                            'destroy'  => 1,
                            'size'     => array(
                                'maxWidth' => 400
                            )
                        ),
                        'content' => $this->getLayout()->createBlock('core/template')
                            ->setTemplate('tm/firecheckout/emailexists.phtml')
                            ->toHtml()
                    );
                } else {
                    $result['error_messages'] = $result['message'];
                }

                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return;
            }

            if ((!isset($billing['use_for_shipping']) || !$billing['use_for_shipping'])
                && !$quote->isVirtual()) {

                $result = $checkout->saveShipping(
                    $shipping,
                    $this->getRequest()->getPost('shipping_address_id', false)
                );
                if ($result) {
                    $result['success'] = false;
                    $result['error']   = true;
                    $result['error_messages'] = $result['message'];
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
            }

            if ('relaypoint_relaypoint' == $this->getRequest()->getPost('shipping_method', false)) {
                $this->relaypointChangeAddress();
            } elseif ('storepickup_storepickup' == $this->getRequest()->getPost('shipping_method', false)) {
                // Magestore_Storepickup
                $storepickup = Mage::getSingleton('checkout/session')->getData('storepickup_session');
                if ($storepickup && isset($storepickup['store_id']) && $storepickup['store_id']) {
                    $this->storepickupChangeAddress();
                }
            }

            $checkoutHelper = Mage::helper('checkout');
            $checkoutHelper->getCheckout()->unsFirecheckoutApprovedAgreementIds();
            $requiredAgreements = $checkoutHelper->getRequiredAgreementIds();
            if ($requiredAgreements) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                $diff = array_diff($requiredAgreements, $postedAgreements);
                if ($diff) {
                    $result['success'] = false;
                    $result['error']   = true;
                    $result['error_messages'] = $checkoutHelper->__('Please agree to all the terms and conditions before placing the order.');
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
                $checkoutHelper->getCheckout()->setFirecheckoutApprovedAgreementIds($postedAgreements);
            }

            $result = $this->_savePayment();
            if ($result && !isset($result['redirect'])) {
                $result['error_messages'] = $result['error'];
            }

            $quote->collectTotals();

            if (!isset($result['error'])) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$this->getRequest(), 'quote'=>$quote));
                if ($quote->getCheckoutMethod() == TM_FireCheckout_Model_Type_Standard::METHOD_GUEST) {
                    $this->_subscribeToNewsletter();
                } elseif ($this->getRequest()->getPost('newsletter')) {
                    $quote->getCustomer()->setIsSubscribed(1);
                }
            }

            // Sales representative integration
            if (Mage::getStoreConfig('salesrep/setup/enabled')
                && $salesRep = $this->getRequest()->getPost('getvoice')) {

                Mage::getSingleton('core/session')->setSalesrep($salesRep);
            }
            // End of Sales representative integration

            // 3D Secure
            $method = $quote->getPayment()->getMethodInstance();
            if ($method->getIsCentinelValidationEnabled()) {
                $centinel = $method->getCentinelValidator();
                if ($centinel && $centinel->shouldAuthenticate()) {
                    $layout = $this->getLayout();
                    $update = $layout->getUpdate();
                    $update->load('firecheckout_index_saveorder');
                    $this->_initLayoutMessages('checkout/session');
                    $layout->generateXml();
                    $layout->generateBlocks();
                    return $this->getResponse()->setBody(Zend_Json::encode(array(
                        'method'            => 'centinel',
                        'update_section'    => array(
                            'centinel-iframe' => $layout->getBlock('centinel.frame')->toHtml()
                        )
                    )));
                }
            }
            // 3D Secure

            $paymentData = $this->getRequest()->getPost('payment', array());
            if ($paymentData && @defined('Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT')) {
                $paymentData['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT
                    | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
                    | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
                    | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
                    | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;
            }

            // SagePay Server
//            $sagePaySuiteMethods = array(
//                'sagepayserver',
//                'sagepayform',
//                'sagepaydirectpro'
//            );
//            if (in_array($paymentData['method'], $sagePaySuiteMethods)) {
//                return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
//                    'method' => 'sagepayserver',
//                    'update_section' => array(
//                        'sagepay-iframe' => $this->getLayout()
//                            ->createBlock('sagepayserver/checkout_onepage_review_info')
//                            ->setTemplate('tm/firecheckout/sagepay/iframe.phtml')
//                            ->toHtml()
//                    )
//                )));
//            }
            // SagePay Server

            // Sage Pay Suite
            $sagePaySuiteMethods = array(
                'sagepayserver',
                'sagepayform',
                'sagepaypaypal',
                'sagepaydirectpro'
            );
            if (in_array($paymentData['method'], $sagePaySuiteMethods)) {
                $quote->save();
                return $this->getResponse()
                    ->setBody(Mage::helper('core')->jsonEncode(array(
                        'method' => $paymentData['method']
                    )));
            }
            // Sage Pay Suite

            // Authorize.Net
            if (!$this->getRequest()->getBeforeForwardInfo() // if forwarded, then we already did the translaction request to authorize.net
                && 'authorizenet_directpost' === $paymentData['method']) {

                $quote->save();
                $layout = $this->getLayout();
                $update = $layout->getUpdate();
                $update->load('firecheckout_index_saveorder');
                $this->_initLayoutMessages('checkout/session');
                $layout->generateXml();
                $layout->generateBlocks();
                return $this->getResponse()
                    ->setBody(Mage::helper('core')->jsonEncode(array(
                        'method' => $paymentData['method'],
                        'popup' => array(
                            'id'      => $paymentData['method'],
                            'content' => $layout->getBlock('payment.form.directpost')->toHtml()
                        )
                    ))
                );
            }
            // Authorize.Net

            if (!isset($result['redirect']) && !isset($result['error'])) {
                if ($paymentData) {
                    $quote->getPayment()->importData($paymentData);
                }

                $checkout->saveOrder();

                $paymentHelper = Mage::helper("payment");
                if (method_exists($paymentHelper, 'getZeroSubTotalPaymentAutomaticInvoice')) {
                    $storeId = Mage::app()->getStore()->getId();
                    $zeroSubTotalPaymentAction = $paymentHelper->getZeroSubTotalPaymentAutomaticInvoice($storeId);
                    if ($paymentHelper->isZeroSubTotal($storeId)
                            && $this->_getOrder()->getGrandTotal() == 0
                            && $zeroSubTotalPaymentAction == Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE
                            && $paymentHelper->getZeroSubTotalOrderStatus($storeId) == 'pending') {
                        $invoice = $this->_initInvoice();
                        $invoice->getOrder()->setIsInProcess(true);
                        $invoice->save();
                    }
                }

                $redirectUrl = $checkout->getCheckout()->getRedirectUrl();
                $result['success'] = true;
                $result['order_created'] = true;
                $result['error']   = false;
            } elseif (isset($result['redirect'])) {
                // paypal express register customer fix
                if ('paypal_express' == $paymentData['method']
                    && version_compare(Mage::helper('firecheckout')->getMagentoVersion(), '1.6.1.0') < 0 // 1.6.1 can register customer during express checkout
                    && Mage::getStoreConfig('firecheckout/general/paypalexpress_register')) {

                    $checkout->registerCustomerIfRequested();
                }
            }
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($quote, $e->getMessage());
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();

            if ($gotoSection = $checkout->getCheckout()->getGotoSection()) {
                $result['goto_section'] = $gotoSection;
                $checkout->getCheckout()->setGotoSection(null);
            }

            if ($updateSection = $checkout->getCheckout()->getUpdateSection()) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {

                    $layout = $this->getUpdateCheckoutLayout();

                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $result['update_section'] = array(
                        'name' => $updateSection,
                        'html' => $this->$updateSectionFunction()
                    );
                }
                $checkout->getCheckout()->setUpdateSection(null);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($quote, $e->getMessage());
            $result['success']  = false;
            $result['error']    = true;
            $result['error_messages'] = Mage::helper('checkout')->__('There was an error processing your order. Please contact us or try again later.');
        }
        $quote->save();
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }

        // paypal hss
        if (empty($result['error']) && file_exists(BP . DS . 'app/code/core/Mage/Paypal/Helper/Hss.php')) {
            $payment = $quote->getPayment();
            if ($payment && in_array($payment->getMethod(), Mage::helper('paypal/hss')->getHssMethods())) {
                $layout = $this->getLayout();
                $update = $layout->getUpdate();
                $update->load('firecheckout_index_saveorder');
                $this->_initLayoutMessages('checkout/session');
                $layout->generateXml();
                $layout->generateBlocks();
                $result = array(
                    'method' => 'paypalhss',
                    'popup' => array(
                        'id'      => $payment->getMethod(),
                        'modal'   => 1,
                        'content' => $layout->getBlock('paypal.iframe')->toHtml()
                    )
                );
                $result['redirect'] = false;
                $result['success'] = false;
            }
        }
        // paypal hss

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}
