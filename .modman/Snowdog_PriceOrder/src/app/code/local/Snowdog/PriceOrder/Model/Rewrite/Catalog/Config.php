<?php

/**
 * Class Snowdog_PriceOrder_Model_Rewrite_Catalog_Config
 */
class Snowdog_PriceOrder_Model_Rewrite_Catalog_Config
    extends Mage_Catalog_Model_Config
{

    /**
     * Retrieve Attributes Used for Sort by as array
     * key = code, value = name
     *
     * @return array
     */
    public function getAttributeUsedForSortByArray()
    {
        $options = array(
            'position'  => Mage::helper('catalog')->__('Position'),
            'lowest_price' => Mage::helper('catalog')->__('Price'),
            'recentlyadded' => Mage::helper('catalog')->__('New Arrivals')
        );
        
        foreach ($this->getAttributesUsedForSortBy() as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute_Abstract */
            $options[$attribute->getAttributeCode()] = $attribute->getStoreLabel();
        }

        return $options;
    }

}