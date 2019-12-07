<?php

class Snowdog_AmazonProducts_Model_Catalog_Product extends Mage_Catalog_Model_Product {

    /**
     * Retrieve array of custom products
     *
     * @return array
     */
    public function getAmazonProducts() {
        if (!$this->hasAmazonProducts()) {
            $products = array();
            $collection = $this->getAmazonProductCollection();
            foreach ($collection as $product) {
                $products[] = $product;
            }
            $this->setAmazonProducts($products);
        }
        return $this->getData('amazon_products');
    }

    /**
     * Retrieve custom products identifiers
     *
     * @return array
     */
    public function getAmazonProductIds() {
        if (!$this->hasAmazonProductIds()) {
            $ids = array();
            foreach ($this->getAmazonProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setAmazonProductIds($ids);
        }
        return $this->getData('amazon_product_ids');
    }

    /**
     * Retrieve collection custom product
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function getAmazonProductCollection() {
        $collection = $this->getLinkInstance()->useAmazonLinks()
                ->getProductCollection()
                ->setIsStrongMode();
        $collection->setProduct($this);
        return $collection;
    }

    /**
     * Retrieve collection custom link
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Collection
     */
    public function getAmazonLinkCollection() {
        $collection = $this->getLinkInstance()->useAmazonLinks()
                ->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }

}
