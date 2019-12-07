<?php
class Justuno_Social_Block_Domain extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
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
			$html.="<span>Justuno Dashboard <a href=".$dashboard." target='_blank'> Click here </a></span>";
		}
		else{
			$justuno_link = Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit/section/justuno");
			$html.="<span>Please <a href=".$justuno_link."> click here </a> to first update your magento / justuno app settings.</span>";
        }

        $html .= $this->_getFooterHtml($element);
        return $html;
    }
}