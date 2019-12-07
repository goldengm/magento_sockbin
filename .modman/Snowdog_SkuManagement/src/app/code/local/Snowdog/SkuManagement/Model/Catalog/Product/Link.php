<?php

/**
 * Class Snowdog_SkuManagement_Model_Catalog_Product_Link
 */
class Snowdog_SkuManagement_Model_Catalog_Product_Link
    extends Mage_Catalog_Model_Product_Link
{

    const LINK_TYPE_CUSTOM          = 7;
    const LINK_TYPE_CUSTOM_TEXT     = 'skumanagement';

    /**
     * @return Mage_Catalog_Model_Product_Link
     */
    public function useSkumanagementLinks() {
        $this->setLinkTypeId(self::LINK_TYPE_CUSTOM);
        return $this;
    }

    /**
     * Save data for product relations
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Catalog_Model_Product_Link
     */
    public function saveProductRelations($product) {
        parent::saveProductRelations($product);

        $data = $product->getSkumanagementLinkData();

        if (!is_null($data)) {
            $this->_getResource()->saveProductLinks(
                $product,
                $data,
                self::LINK_TYPE_CUSTOM
            );
        }
    }

}
