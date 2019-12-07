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

class Webtex_Fba_Block_Adminhtml_Marketplace_Edit_Tab_Inventory
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

        $form->setHtmlIdPrefix('marketplace_inventory');

        $fieldset = $form->addFieldset('inventory_fieldset', array('legend' => Mage::helper('fba')->__('Inventory Settings')));

//        if ($model->getId()) {
//            $fieldset->addField('id', 'hidden', array(
//                'name' => 'id',
//            ));
//        }

        $mode = $fieldset->addField('inventory_mode', 'select', array(
            'name' => 'inventory_mode',
            'label' => Mage::helper('fba')->__('Inventory Mode'),
            'title' => Mage::helper('fba')->__('Inventory Mode'),
            'required' => true,
            'options' => Mage::getModel('fba/config_source_inventoryMode')->toArray(),
            'disabled' => $isElementDisabled
        ));

        $checkOrder = $fieldset->addField('check_qty_before_place_order', 'select', array(
            'name' => 'check_qty_before_place_order',
            'label' => Mage::helper('fba')->__('Check cart products qty before place order?'),
            'title' => Mage::helper('fba')->__('Check cart products qty before place order?'),
            'required' => true,
            'options' => Mage::getModel('fba/config_source_yesno')->toArray(),
            'disabled' => $isElementDisabled
        ));

        $qtyField = $fieldset->addField('qty_check_field', 'select', array(
            'name' => 'qty_check_field',
            'label' => Mage::helper('fba')->__('Amazon Quantity type field to check'),
            'title' => Mage::helper('fba')->__('Amazon Quantity type field to check'),
            'required' => true,
            'options' => Mage::getModel('fba/config_source_amazonQtyField')->toArray(),
            'disabled' => $isElementDisabled
        ));

        $shipOos = $fieldset->addField('ship_oos_as_non_fba', 'select', array(
            'name' => 'ship_oos_as_non_fba',
            'label' => Mage::helper('fba')->__('Ship products which out of stock in Amazon as non-FBA products'),
            'title' => Mage::helper('fba')->__('Ship products which out of stock in Amazon as non-FBA products'),
            'after_element_html' => "<small>" . Mage::helper('fba')->__('Works only with "Manage Stock->No" products') . "</small>",
            'required' => true,
            'options' => Mage::getModel('fba/config_source_yesno')->toArray(),
            'disabled' => $isElementDisabled
        ));

        $form->setValues($model->getData());
        $this->setForm($form);
        $dependence = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence');
        $dependence->addFieldMap($mode->getHtmlId(), $mode->getName())
            ->addFieldMap($checkOrder->getHtmlId(), $checkOrder->getName())
            ->addFieldMap($qtyField->getHtmlId(), $qtyField->getName())
            ->addFieldMap($shipOos->getHtmlId(), $shipOos->getName())
            ->addFieldDependence(
                $checkOrder->getName(),
                $mode->getName(),
                Webtex_Fba_Model_Config_Source_InventoryMode::AMAZON_MODE)
            ->addFieldDependence(
                $shipOos->getName(),
                $mode->getName(),
                Webtex_Fba_Model_Config_Source_InventoryMode::AMAZON_MODE)
            ->addFieldDependence(
                $qtyField->getName(),
                $mode->getName(),
                Webtex_Fba_Model_Config_Source_InventoryMode::AMAZON_MODE);
        $this->setChild('form_after', $dependence);

        return parent::_prepareForm();
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('fba')->__('Inventory Settings');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('fba')->__('Inventory Settings');
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