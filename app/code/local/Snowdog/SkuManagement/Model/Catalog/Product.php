<?php

/**
 * Class Snowdog_SkuManagement_Model_Catalog_Product
 */
class Snowdog_SkuManagement_Model_Catalog_Product
    extends Mage_Catalog_Model_Product
{

    /**
     * Retrieve array of linked products
     *
     * @return array
     */
    public function getSkuManagementProducts()
    {
        if (!$this->hasSkumanagementProducts()) {
            $products = array();
            $collection = $this->getSkumanagementProductCollection();

            foreach ($collection as $product) {
                $products[] = $product;
            }

            $this->setSkumanagementProducts($products);
        }

        return $this->getData('skumanagement_products');
    }

    /**
     * Retrieve linked products identifiers
     *
     * @return array
     */
    public function getSkuManagementProductIds()
    {
        if (!$this->hasSkumanagementProductIds()) {
            $ids = array();

            foreach ($this->getSkumanagementProducts() as $product) {
                $ids[] = $product->getId();
            }

            $this->setSkumanagementProductIds($ids);
        }

        return $this->getData('skumanagement_product_ids');
    }

    /**
     * Retrieve collection linked product
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function getSkuManagementProductCollection()
    {
        $collection = $this->getLinkInstance()
            ->useSkumanagementLinks()
            ->getProductCollection()
            ->addAttributeToSelect('pack_size_qty')
            ->setIsStrongMode();

        $collection->setProduct($this);

        return $collection;
    }

    /**
     * Retrieve collection link
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function getSkuManagementLinkCollection()
    {
        $collection = $this->getLinkInstance()
            ->useSkumanagementLinks()
            ->getLinkCollection();

        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();

        return $collection;
    }

}
