<?php

class Snowdog_AmazonProducts_Block_Adminhtml_Amazonskus_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save',
                array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ));

        $form->setUseContainer(true);

        $this->setForm($form);

        $fieldset = $form->addFieldset('amazonskus_form',
            array(
                'legend' => Mage::helper('snowamazonproducts')->__('Upload file')
            ));

        $fieldset->addField('amazonfileskus', 'image',
            array(
                'label' => Mage::helper('snowamazonproducts')->__('File to Import (CSV format)'),
                'required' => true,
                'name' => 'amazonfileskus',
                'class' => 'input-file required-entry',
            ));

        return parent::_prepareForm();
    }
}