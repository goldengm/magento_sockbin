<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Googlebasemin edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Googlebasemin_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('googlebasemin_form');
        $this->setTitle(Mage::helper('adminhtml')->__('Google Base Min Information'));
    }


    protected function _prepareForm()
    {
        $model = Mage::registry('googlebasemin_googlebasemin');

        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));

        $fieldset = $form->addFieldset('add_googlebasemin_form', array('legend' => Mage::helper('googlebasemin')->__('Googlebasemin')));

        if ($model->getId()) {
            $fieldset->addField('googlebasemin_id', 'hidden', array(
                'name' => 'googlebasemin_id',
            ));
        }

        $fieldset->addField('googlebasemin_filename', 'text', array(
            'label' => Mage::helper('googlebasemin')->__('Filename'),
            'name'  => 'googlebasemin_filename',
            'required' => true,
            'note'  => Mage::helper('adminhtml')->__('example: googlebasemin.txt'),
            'value' => $model->getGooglebaseminFilename()
        ));

        $fieldset->addField('googlebasemin_path', 'text', array(
            'label' => Mage::helper('googlebasemin')->__('Path'),
            'name'  => 'googlebasemin_path',
            'required' => true,
            'note'  => Mage::helper('adminhtml')->__('example: "media/sockbin/feeds/" or "/" for base path (path must be writeable)'),
            'value' => $model->getGooglebaseminPath()
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'label'    => Mage::helper('googlebasemin')->__('Store View'),
                'title'    => Mage::helper('googlebasemin')->__('Store View'),
                'name'     => 'store_id',
                'required' => true,
                'value'    => $model->getStoreId(),
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'     => 'store_id',
                'value'    => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('generate', 'hidden', array(
            'name'     => 'generate',
            'value'    => ''
        ));

        $form->setValues($model->getData());

        $form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
