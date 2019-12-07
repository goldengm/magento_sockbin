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

class Webtex_Fba_Block_Adminhtml_Marketplace_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_marketplace';
        $this->_blockGroup = 'fba';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('fba')->__('Save Marketplace'));
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick' => 'editForm.submit($(\'edit_form\').action + \'back/edit/\')',
            'class' => 'save',
        ), -100);

        $this->_addButton('duplicate_button', array(
            'label' => Mage::helper('catalog')->__('Duplicate'),
            'onclick' => 'setLocation(\'' . $this->getDuplicateUrl() . '\')',
            'class' => 'add'
        ), -100);


        $this->_updateButton('delete', 'label', Mage::helper('fba')->__('Delete Marketplace'));
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('mws_marketplace')->getId()) {
            return Mage::helper('fba')->__("Edit Marketplace '%s'", $this->htmlEscape(Mage::registry('mws_marketplace')->getCode()));
        } else {
            return Mage::helper('fba')->__('New Marketplace');
        }
    }


    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current' => true,
            'back' => 'edit',
            'active_tab' => '{{tab_id}}'
        ));
    }

    public function getDuplicateUrl()
    {
        return $this->getUrl('*/*/duplicate', array('_current' => true));
    }

}