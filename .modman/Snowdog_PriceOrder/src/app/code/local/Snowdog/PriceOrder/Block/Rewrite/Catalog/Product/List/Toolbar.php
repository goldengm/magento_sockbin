<?php

/**
 * Class Snowdog_PriceOrder_Block_Rewrite_Catalog_Product_List_Toolbar
 */
class Snowdog_PriceOrder_Block_Rewrite_Catalog_Product_List_Toolbar
    extends Mage_Catalog_Block_Product_List_Toolbar
{

    protected $_direction = 'asc';

    /**
     * Set collection to pager
     *
     * @param Varien_Data_Collection $collection
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        /* SNOWDOG REWRITE starts: adding tier price for the lowest qty */
        $this->_collection->getSelect()
            ->joinLeft(
                ['cpetp' => 'catalog_product_entity_tier_price'],
                'cpetp.entity_id = e.entity_id AND cpetp.qty = ( 
                    SELECT min(dd.qty) 
                         FROM catalog_product_entity_tier_price dd
                         WHERE e.entity_id = dd.entity_id
                       )',
                ['lowest_price' => 'value']
            );
        /* SNOWDOG REWRITE ends */
        
        // we need to set pagination only if passed value integer and more that 0
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }

        if ($this->getCurrentOrder()) {
            if ($this->getCurrentOrder() == 'recentlyadded') {
                $currentOrder = 'entity_id';
            } elseif ($this->getCurrentOrder() == 'price') {
                $currentOrder = 'lowest_price';
            } else {
                $currentOrder = $this->getCurrentOrder();
            }

            $this->_collection->getSelect()
                ->order($currentOrder . ' ' . $this->getCurrentDirection());
        }

        return $this;
    }

}