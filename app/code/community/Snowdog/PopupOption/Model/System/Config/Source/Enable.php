<?php
class Snowdog_PopupOption_Model_System_Config_Source_Enable
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
		
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Off')),
            array('value' => 'block', 'label'=>Mage::helper('adminhtml')->__('Top block only')),
            array('value' => 'block_email', 'label'=>Mage::helper('adminhtml')->__('Top block and email singup')),
        );
    }

}
