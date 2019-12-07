<?php
class Justuno_Social_Block_Dashboard extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {

    	$email = Mage::getStoreConfig('justuno/account/email',0);
		$jusdata = Mage::getStoreConfig('justuno/account/embed',0);
		if ($jusdata) {
			$jusdata = json_decode($jusdata);
		}
		$dashboard = $jusdata->dashboard;
		
        $this->setElement($element);
        $html = $this->_getHeaderHtml($element);

        if ($email && $jusdata) {
			$html.= "<span>You are already connected. <a href=".Mage::helper('adminhtml')->getUrl("adminhtml/custom/index/").">Click here to disconnect if necessary</a></span>";
		}
		else{
	        foreach ($element->getSortedElements() as $field) {
	            $html.= $field->toHtml();
	        }
        }

        $html .= $this->_getFooterHtml($element);
        return $html;
    }
}
