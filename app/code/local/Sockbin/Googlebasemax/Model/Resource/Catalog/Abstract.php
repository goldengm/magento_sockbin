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
 * @package     Sockbin_Googlebasemax
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Googlebasemax resource catalog collection model
 *
 * @category    Sockbin
 * @package     Sockbin_Googlebasemax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Sockbin_Googlebasemax_Model_Resource_Catalog_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Collection Zend Db select
     *
     * @var Zend_Db_Select
     */
    protected $_select;

    /**
     * Attribute cache
     *
     * @var array
     */
    protected $_attributesCache = array();

    /**
     * Catalog factory instance
     *
     * @var Mage_Catalog_Model_Factory
     */
    protected $_factory;

    /**
     * Initialize factory instance
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('catalog/factory');
        parent::__construct();
    }

    /**
     * Retrieve catalog collection
     *
     * @param int $storeId
     * @return array
     */
    abstract public function getCollection($storeId);

    /**
     * Add attribute to filter
     *
     * @param int $storeId
     * @param string $attributeCode
     * @param mixed $value
     * @param string $type
     * @return Zend_Db_Select
     */
    protected function _addFilter($storeId, $attributeCode, $value, $type = '=')
    {
        if (!isset($this->_attributesCache[$attributeCode])) {
            $this->_loadAttribute($attributeCode);
        }

        $attribute = $this->_attributesCache[$attributeCode];

        if (!$this->_select instanceof Zend_Db_Select) {
            return false;
        }

        switch ($type) {
            case '=':
                $conditionRule = '=?';
                break;
            case 'in':
                $conditionRule = ' IN(?)';
                break;
            default:
                return false;
                break;
        }

        if ($attribute['backend_type'] == 'static') {
            $this->_select->where('main_table.' . $attributeCode . $conditionRule, $value);
        } else {
            $this->_select->join(
                array('t1_' . $attributeCode => $attribute['table']),
                'main_table.entity_id=t1_' . $attributeCode . '.entity_id AND t1_' . $attributeCode . '.store_id=0',
                array()
            )
                ->where('t1_' . $attributeCode . '.attribute_id=?', $attribute['attribute_id']);

            if ($attribute['is_global']) {
                $this->_select->where('t1_' . $attributeCode . '.value' . $conditionRule, $value);
            } else {
                $ifCase = $this->_select->getAdapter()->getCheckSql('t2_' . $attributeCode . '.value_id > 0',
                    't2_' . $attributeCode . '.value', 't1_' . $attributeCode . '.value'
                );
                $this->_select->joinLeft(
                    array('t2_' . $attributeCode => $attribute['table']),
                    $this->_getWriteAdapter()->quoteInto(
                        't1_' . $attributeCode . '.entity_id = t2_' . $attributeCode . '.entity_id AND t1_'
                            . $attributeCode . '.attribute_id = t2_' . $attributeCode . '.attribute_id AND t2_'
                            . $attributeCode . '.store_id = ?', $storeId
                    ),
                    array()
                )
                ->where('(' . $ifCase . ')' . $conditionRule, $value);
            }
        }

        return $this->_select;
    }

    /**
     * Prepare catalog object
     *
     * @param array $row
     * @return Varien_Object
     */
    protected function _prepareObject(array $row)
    {
        $_product = Mage::getModel('catalog/product')->load($row[$this->getIdFieldName()])->getData();
        $categoryIds = Mage::getModel('catalog/product')->load($row[$this->getIdFieldName()])->getCategoryIds();
        $categories = array();
        foreach ($categoryIds as $categoryId) {
            $_cat = Mage::getModel('catalog/category')->load($categoryId);
            $_parent_cat = Mage::getModel('catalog/category')->load($_cat['parent_id']);
            $categories[] = array(
                'category_name' => $_cat['name'],
                'parent_category_name' => $_parent_cat['name']
            );
        }
        
        $entity = new Varien_Object();
        $entity->setId($row[$this->getIdFieldName()]);
        $entity->setUrl($this->_getEntityUrl($row, $entity));
        $entity->setName($_product['name']);
        
        $tierPrices = (!empty($_product['tier_price'])) ? $_product['tier_price']: array('');
        $maxTierPrice = '';
        foreach ($tierPrices as $tierPrice) {
            if (bccomp($tierPrice['price'], $maxTierPrice) === 1) {
                $maxTierPrice = $tierPrice['price'];
            }
        }
        $entity->setPrice((!empty($maxTierPrice)) ? strval(number_format($maxTierPrice, 2)): strval(number_format($_product['price'], 2)));
        
        $entity->setImage((!empty($_product['image'])) ? $_product['image']: '');
        $entity->setImage1((!empty($_product['images'][1]['file'])) ? $_product['images'][1]['file']: '');
        $entity->setSku($_product['sku']);
        $entity->setDescription((!empty($_product['description'])) ? $_product['description']: '');
        $entity->setInventory($_product['quantity']);
        $entity->setCategory((!empty($categories[0]['category_name'])) ? ' > ' . $categories[0]['category_name']: '');
        $entity->setParentCategory((!empty($categories[0]['parent_category_name'])) ? ' > ' . $categories[0]['parent_category_name']: '');
        return $entity;
    }

    /**
     * Load and prepare entities
     *
     * @return array
     */
    protected function _loadEntities()
    {
        $entities = array();
        $query = $this->_getWriteAdapter()->query($this->_select);
        while ($row = $query->fetch()) {
            $entity = $this->_prepareObject($row);
            $entities[$entity->getId()] = $entity;
        }
        return $entities;
    }

    /**
     * Retrieve entity url
     *
     * @param array $row
     * @param Varien_Object $entity
     * @return string
     */
    abstract protected function _getEntityUrl($row, $entity);

    /**
     * Loads attribute by given attribute_code
     *
     * @param string $attributeCode
     * @return Sockbin_Googlebasemax_Model_Resource_Catalog_Abstract
     */
    abstract protected function _loadAttribute($attributeCode);
}
