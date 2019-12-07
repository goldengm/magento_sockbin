<?php
/**
 * Webtex
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.webtexsoftware.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@webtexsoftware.com and we will send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.webtexsoftware.com for more information,
 * or contact us through this email: info@webtexsoftware.com.
 *
 * @category   Webtex
 * @package    Webtex_Fba
 * @copyright  Copyright (c) 2011 Webtex Solutions, LLC (http://www.webtexsoftware.com/)
 * @license    http://www.webtexsoftware.com/LICENSE.txt End-User License Agreement
 */

class Webtex_Fba_Block_Adminhtml_Marketplace_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected function _prepareForm()
    {
        /** @var $model Webtex_Fba_Model_Mws_Marketplace */
        $model = Mage::registry('mws_marketplace');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('marketplace_main');

        $fieldset = $form->addFieldset('main_fieldset', array('legend' => Mage::helper('fba')->__('Marketplace Information')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name' => 'id',
            ));
        }

        $fieldset->addField('status', 'select', array(
            'name' => 'status',
            'label' => Mage::helper('fba')->__('Marketplace Status'),
            'title' => Mage::helper('fba')->__('Marketplace Status'),
            'required' => true,
            'options' => Webtex_Fba_Block_Adminhtml_Marketplace_Renderer_Status::getStatusOptions(),
            'disabled' => $isElementDisabled
        ));

        $fieldset->addField('access_key_id', 'text', array(
            'name' => 'access_key_id',
            'label' => Mage::helper('fba')->__('Access Key Id'),
            'title' => Mage::helper('fba')->__('Access Key Id'),
            'required' => true,
            'class' => 'validate-length minimum-length-20 maximum-length-20',
            'after_element_html' => '<small>alphanumeric sequence (20 character)</small>',
            'disabled' => $isElementDisabled
        ));

        $fieldset->addField('plain_secret_key', 'text', array(
            'name' => 'plain_secret_key',
            'label' => Mage::helper('fba')->__('Secret Key'),
            'title' => Mage::helper('fba')->__('Secret Key'),
            'required' => true,
            'class' => 'validate-length minimum-length-40 maximum-length-40',
            'after_element_html' => '<small>alphanumeric sequence (40 character)</small>',
            'disabled' => $isElementDisabled
        ));

        $fieldset->addField('merchant_id', 'text', array(
            'name' => 'merchant_id',
            'label' => Mage::helper('fba')->__('Merchant Id'),
            'title' => Mage::helper('fba')->__('Merchant Id'),
            'required' => true,
            'after_element_html' => '<small>alphanumeric sequence </small>',
            'disabled' => $isElementDisabled
        ));

        $fieldset->addField('amazon_marketplace', 'select', array(
            'name' => 'amazon_marketplace',
            'label' => Mage::helper('fba')->__('Amazon Marketplace'),
            'title' => Mage::helper('fba')->__('Amazon Marketplace'),
            'required' => true,
            'options' => Mage::getModel('fba/config_source_amazonMarketplace')->toArray(),
            'disabled' => $isElementDisabled
        ));

        $fieldset->addField('notification_emails', 'text', array(
            'name' => 'notification_emails',
            'label' => Mage::helper('fba')->__('Notification Emails'),
            'title' => Mage::helper('fba')->__('Notification Emails'),
            'after_element_html' => '<small>comma-separated list of emails</small>',
            'disabled' => $isElementDisabled
        ));

        $fieldset->addField('notify_customers', 'select', array(
            'name' => 'notify_customers',
            'label' => Mage::helper('fba')->__('Send Amazon delivery status email to customer?'),
            'title' => Mage::helper('fba')->__('Send Amazon delivery status email to customer?'),
            'required' => true,
            'options' => Mage::getModel('fba/config_source_yesno')->toArray(),
            'disabled' => $isElementDisabled
        ));

        $fieldset->addField('send_order_immediately', 'select', array(
            'name' => 'send_order_immediately',
            'label' => Mage::helper('fba')->__('Order Auto Send'),
            'title' => Mage::helper('fba')->__('Order Auto Send'),
            'required' => true,
            'options' => Mage::getModel('fba/config_source_send')->toArray(),
            'disabled' => $isElementDisabled
        ));


        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('fba')->__('Marketplace Information');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('fba')->__('Marketplace Information');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }

}