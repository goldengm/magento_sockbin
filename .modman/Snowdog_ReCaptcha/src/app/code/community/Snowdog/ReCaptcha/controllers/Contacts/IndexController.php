<?php

require_once Mage::getModuleDir('controllers', 'Mage_Contacts') . DS . 'IndexController.php';

/**
 * Class Snowdog_ReCaptcha_Contacts_IndexController
 */
class Snowdog_ReCaptcha_Contacts_IndexController
    extends Mage_Contacts_IndexController
{
    /**
     * Contact post controller
     */
    public function postAction()
    {
        $post = $this->getRequest()->getPost();

        if ($post) {
            if (isset($post['g-recaptcha-response'])) {
                if (empty($post['g-recaptcha-response'])) {
                    Mage::getSingleton('customer/session')
                        ->addError(
                            Mage::helper('contacts')
                                ->__('Please verify reCaptcha. It is required field.')
                        );
                    $this->_redirect('*/*/');
                    return;
                } else {
                    $captchaPassed = $this
                        ->verifyReCaptcha($post['g-recaptcha-response']);

                    if (!$captchaPassed) {
                        Mage::getSingleton('customer/session')
                            ->addError(
                                Mage::helper('contacts')
                                    ->__('Invalid reCaptcha given.')
                            );
                        $this->_redirect('*/*/');
                        return;
                    }
                }
            }
            
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);

            try {
                $postObject = new Varien_Object();
                $postObject->setData($post);

                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }
                $mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                        null,
                        array('data' => $postObject)
                    );

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/');
                return;
            }

        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Verify captcha
     *
     * @param $response
     *
     * @return mixed
     */
    function verifyReCaptcha($response)
    {
        $url = 'https://www.google.com/recaptcha/api/siteverify';

        $data = [
            'secret' => Mage::helper('snowrecaptcha')->getSecretKey(),
            'response' => $response,
            'remoteip' => $this->getRequest()->getClientIp(true)
        ];

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        $responseDecode = Mage::helper('core')
            ->jsonDecode($response);

        if (isset($responseDecode['success'])) {
            return $responseDecode['success'];
        }

        return false;
    }

}