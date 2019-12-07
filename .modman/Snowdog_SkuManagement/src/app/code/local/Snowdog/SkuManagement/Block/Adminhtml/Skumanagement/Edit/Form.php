<?php

/**
 * Class Snowdog_SkuManagement_Block_Adminhtml_Skumanagement_Edit_Form
 */
class Snowdog_SkuManagement_Block_Adminhtml_Skumanagement_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     * @throws Exception
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl(
                '*/*/save',
                array('id' => $this->getRequest()->getParam('id'))
            ),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $form->setUseContainer(true);

        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'skumanagement_form',
            array(
                'legend' => Mage::helper('snowskumanagement')->__('Upload file')
            )
        );

        $fieldset->addField(
            'skumanagementfileskus',
            'image',
            array(
                'label' => Mage::helper('snowskumanagement')->__('File to Import (CSV format)'),
                'required' => true,
                'name' => 'skumanagementfileskus',
                'class' => 'input-file required-entry',
            )
        );

        return parent::_prepareForm();
    }

}