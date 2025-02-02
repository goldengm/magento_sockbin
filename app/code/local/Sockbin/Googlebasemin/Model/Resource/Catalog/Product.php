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
 * @package     Sockbin_Googlebasemin
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Googlebasemin resource product collection model
 *
 * @category    Sockbin
 * @package     Sockbin_Googlebasemin
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Sockbin_Googlebasemin_Model_Resource_Catalog_Product extends Sockbin_Googlebasemin_Model_Resource_Catalog_Abstract
{
    /**
     * Init resource model (catalog/product)
     */
    protected function _construct()
    {
        $this->_init('catalog/product', 'entity_id');
    }

    /**
     * Get product collection array
     *
     * @param int $storeId
     * @return array
     */
    public function getCollection($storeId)
    {
        /* @var $store Mage_Core_Model_Store */
        $store = Mage::app()->getStore($storeId);
        if (!$store) {
            return false;
        }

        $this->_select = $this->_getWriteAdapter()->select()
            ->from(array('main_table' => $this->getMainTable()), array($this->getIdFieldName()))
            ->join(
                array('w' => $this->getTable('catalog/product_website')),
                'main_table.entity_id = w.product_id',
                array()
            )
            ->where('w.website_id=?', $store->getWebsiteId());

        $storeId = (int)$store->getId();

        /** @var $urlRewrite Mage_Catalog_Helper_Product_Url_Rewrite_Interface */
        $urlRewrite = $this->_factory->getProductUrlRewriteHelper();
        $urlRewrite->joinTableToSelect($this->_select, $storeId);

        $this->_addFilter($storeId, 'visibility',
            Mage::getSingleton('catalog/product_visibility')->getVisibleInSiteIds(), 'in'
        );
        $this->_addFilter($storeId, 'status',
            Mage::getSingleton('catalog/product_status')->getVisibleStatusIds(), 'in'
        );

        return $this->_loadEntities();
    }

    /**
     * Prepare product
     *
     * @deprecated after 1.7.0.2
     *
     * @param array $productRow
     * @return Varien_Object
     */
    protected function _prepareProduct(array $productRow)
    {
        return $this->_prepareObject($productRow);
    }

    /**
     * Retrieve entity url
     *
     * @param array $row
     * @param Varien_Object $entity
     * @return string
     */
    protected function _getEntityUrl($row, $entity)
    {
        return !empty($row['request_path']) ? $row['request_path'] : 'catalog/product/view/id/' . $entity->getId();
    }

    /**
     * Loads product attribute by given attribute code
     *
     * @param string $attributeCode
     * @return Sockbin_Googlebasemin_Model_Resource_Catalog_Abstract
     */
    protected function _loadAttribute($attributeCode)
    {
        $attribute = Mage::getSingleton('catalog/product')->getResource()->getAttribute($attributeCode);

        $this->_attributesCache[$attributeCode] = array(
            'entity_type_id' => $attribute->getEntityTypeId(),
            'attribute_id'   => $attribute->getId(),
            'table'          => $attribute->getBackend()->getTable(),
            'is_global'      => $attribute->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'backend_type'   => $attribute->getBackendType()
        );
        return $this;
    }
}
