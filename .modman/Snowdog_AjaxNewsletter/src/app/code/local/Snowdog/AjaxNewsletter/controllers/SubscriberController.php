<?php

/**
 * Class Snowdog_AjaxNewsletter_SubscriberController
 */
class Snowdog_AjaxNewsletter_SubscriberController 
    extends Mage_Core_Controller_Front_Action
{

    /**
     * Newsletter New Ajax Action.
     */
    public function newAjaxAction()
    {

        $response = array();

        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $customerSession = Mage::getSingleton('customer/session');
            $email           = (string) $this->getRequest()->getPost('email');
 
            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    $message = $this->__('Please enter a valid email address.');
                    $response['status'] = 'ERROR';
                    $response['message'] = $message;
                }
 
                if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && 
                    !$customerSession->isLoggedIn()) {
                    $message = $this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl());
                    $response['status'] = 'ERROR';
                    $response['message'] = $message;
                }
 
                $ownerId = Mage::getModel('customer/customer')
                        ->setWebsiteId(Mage::app()
                        ->getStore()
                        ->getWebsiteId())
                        ->loadByEmail($email)
                        ->getId();

                if ($ownerId !== NULL && $ownerId != $customerSession->getId()) {
                    $message = $this->__('This email address is already assigned to another user.');
                    $response['status'] = 'ERROR';
                    $response['message'] = $message;
                }

                $emailExist = Mage::getModel('newsletter/subscriber')
                        ->load($email, 'subscriber_email');
 
                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
               
                if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                    $message = $this->__('Confirmation request has been sent.');
                    $response['status'] = 'SUCCESS';
                    $response['message'] = $message;
                
                } elseif ($emailExist->getId()) {
                    $message = $this->__('You have already subscribed to our newsletter.');
                    $response['status'] = 'ERROR';
                    $response['message'] = $message;

                } else {
                    $message = $this->__('You are now subscribed to our newsletter.');
                    $response['status'] = 'SUCCESS';
                    $response['message'] = $message;
                }

            }

            catch (Mage_Core_Exception $e) {
                $message = $this->__('There was a problem with the subscription: %s', $e->getMessage());
                $response['status'] = 'ERROR';
                $response['message'] = $message;
            }

            catch (Exception $e) {
                $message = $this->__('There was a problem with the subscription.');
                $response['status'] = 'ERROR';
                $response['message'] = $message;
            }
       }

       $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
       
       return;
   }
}
