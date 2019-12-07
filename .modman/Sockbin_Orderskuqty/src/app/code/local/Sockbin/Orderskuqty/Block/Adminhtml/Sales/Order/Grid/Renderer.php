<?php

class Sockbin_Orderskuqty_Block_Adminhtml_Sales_Order_Grid_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		return $this->_getItems($row);
	}

	protected function _getItems($row)
	{
		$content = array();
		foreach ($row->getAllItems() as $item) {
			$content[] = $item->getSku().' - '.(int)$item->getQtyOrdered();
		};
		return $content ? implode(",<br>",$content) : "";
	}

	public function renderExport(Varien_Object $row)
    {
        $content = array();
		foreach ($row->getAllItems() as $item) {
			$content[] = $item->getSku().' - '.(int)$item->getQtyOrdered();
		};
		return $content ? implode(",",$content) : "";
    }
 
}
?>