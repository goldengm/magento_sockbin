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

class Webtex_Fba_Model_Mws_Resource_Product extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('mws/product', 'id');
    }

    public function loadByProduct(Mage_Catalog_Model_Product $product, $amazonSku, $marketplaceId)
    {

        if (trim($amazonSku) != "")
            $sku = $amazonSku;
        else
            $sku = $product->getSku();
//        $sku = addslashes(Mage::getSingleton('core/resource')->getConnection('default_write')->quote($sku));
        $select = $this->_getReadAdapter()->select()->from($this->getTable('mws/product'))
            ->where("`sku`='" . $sku . "' AND `fba_marketplace_id`=" . $marketplaceId);
        return $this->_getReadAdapter()->fetchRow($select);
    }
}
