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

class Webtex_Fba_Block_Adminhtml_Marketplace_Edit_Tab_Shipping
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('fba')->__('Shipping Settings');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('fba')->__('Shipping Settings');
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

        $form->setHtmlIdPrefix('marketplace_shipping');

        $formData = new Varien_Object();

        $dependence = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence');

        foreach (Mage::getModel('fba/config_source_shippingType')->getMethods() as $method) {
            $fieldset = $form->addFieldset('shipping_fieldset_' . $method['code'], array('legend' => Mage::helper('fba')->__(Mage::getModel('fba/config_source_shippingType')->getNameById($method['code']) . ' Shipping')));
            $fieldset->addType('priceRules', 'Webtex_Fba_Block_Adminhtml_Marketplace_Renderer_Pricerules');
            $savedMethod = $model->getShippingSettings($method['code']);


            if ($savedMethod && $savedMethod->getId()) {
                $formData->setData('id_' . $method['code'], $savedMethod->getId());
                $fieldset->addField('id_' . $method['code'], 'hidden', array(
                    'name' => 'shipping[' . $method['code'] . '][id]'
                ));
            }

            $formData->setData('is_active_' . $method['code'], $savedMethod ? $savedMethod->getIsActive() : 0);

            $fieldset->addField('is_active_' . $method['code'], 'select', array(
                'name' => 'shipping[' . $method['code'] . '][is_active]',
                'label' => Mage::helper('fba')->__('Status'),
                'title' => Mage::helper('fba')->__('Status'),
                'required' => false,
                'options' => Webtex_Fba_Block_Adminhtml_Marketplace_Renderer_Status::getStatusOptions(),
                'disabled' => $isElementDisabled
            ));

            $formData->setData('title_' . $method['code'], $savedMethod ? $savedMethod->getTitle() : '');

            $fieldset->addField('title_' . $method['code'], 'text', array(
                'name' => 'shipping[' . $method['code'] . '][title]',
                'label' => Mage::helper('fba')->__('Title'),
                'title' => Mage::helper('fba')->__('Title'),
                'required' => false,
                'disabled' => $isElementDisabled
            ));


            $formData->setData('rules_' . $method['code'], $savedMethod ? $savedMethod->getRules() : array());

            $fieldset->addField('rules_' . $method['code'], 'priceRules', array(
                'name' => 'shipping[' . $method['code'] . '][rules]',
                'label' => Mage::helper('fba')->__('Rules'),
                'title' => Mage::helper('fba')->__('Rules'),
                'required' => false,
                'disabled' => $isElementDisabled
            ));

            $formData->setData('allow_specific_country_' . $method['code'], $savedMethod ? $savedMethod->getAllowSpecificCountry() : 0);

            $allowSpec = $fieldset->addField('allow_specific_country_' . $method['code'], 'select', array(
                'name' => 'shipping[' . $method['code'] . '][allow_specific_country]',
                'label' => Mage::helper('fba')->__('Ship to Applicable Countries'),
                'title' => Mage::helper('fba')->__('Ship to Applicable Countries'),
                'required' => false,
                'values' => Mage::getModel('adminhtml/system_config_source_payment_allspecificcountries')->toOptionArray(),
                'disabled' => $isElementDisabled
            ));

            $formData->setData('country_' . $method['code'], $savedMethod ? $savedMethod->getCountry() : 0);

            $countries = $fieldset->addField('country_' . $method['code'], 'multiselect', array(
                'name' => 'shipping[' . $method['code'] . '][country]',
                'label' => Mage::helper('fba')->__('Ship to Specific Countries'),
                'title' => Mage::helper('fba')->__('Ship to Specific Countries'),
                'required' => false,
                'values' => Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(true),
                'disabled' => $isElementDisabled
            ));

            $formData->setData('is_default_' . $method['code'], $savedMethod ? $savedMethod->getIsDefault() : 0);

            $fieldset->addField('is_default_' . $method['code'], 'select', array(
                'name' => 'shipping[' . $method['code'] . '][is_default]',
                'label' => Mage::helper('fba')->__('Is Method Default'),
                'title' => Mage::helper('fba')->__('Is Method Default'),
                'required' => false,
                'values' => Mage::getModel('fba/config_source_Yesno')->toArray(),
                'disabled' => $isElementDisabled
            ));

            $formData->setData('is_free_' . $method['code'], $savedMethod ? $savedMethod->getIsFree() : 0);

            $fieldset->addField('is_free_' . $method['code'], 'select', array(
                'name' => 'shipping[' . $method['code'] . '][is_free]',
                'label' => Mage::helper('fba')->__('Is Method Free'),
                'title' => Mage::helper('fba')->__('Is Method Free'),
                'required' => false,
                'values' => Mage::getModel('fba/config_source_Yesno')->toArray(),
                'disabled' => $isElementDisabled
            ));

            $dependence->addFieldMap($allowSpec->getHtmlId(), $allowSpec->getName())
                ->addFieldMap($countries->getHtmlId(), $countries->getName())
                ->addFieldDependence(
                    $countries->getName(),
                    $allowSpec->getName(),
                    '1');
        }

        $form->setValues($formData->getData());

        $this->setForm($form);
        $this->setChild('form_after', $dependence);

        return parent::_prepareForm();
    }

    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }

}