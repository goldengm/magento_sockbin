<?php
/*
 * @author  Ivica Tadić <ivica.tadic@ymail.com>
 */

class Tadic_AVP_Block_Adminhtml_Catalog_Product_Edit extends Mage_Adminhtml_Block_Catalog_Product_Edit
{

	public function getHeader()
	{
		$header = parent::getHeader();

		if ($this->getProduct()->getId()) {
            $productUrl = $this->getProduct()->getUrlInStore();
            $previewUrl = $this->getUrl('tadic_avp/product/preview', array(
                'id' => $this->getProductId(),
                'key' => Mage::helper('tadic_avp/catalog_product')->getHashForProduct($this->getProductId()),
            ));
			$header .= "&nbsp&nbsp<a href='$productUrl' target='_blank'>View on web</a>";
            $header .= "&nbsp&nbsp|&nbsp&nbsp<a href='$previewUrl' target='_blank'>Preview</a>";
		}

		return $header;
	}

}