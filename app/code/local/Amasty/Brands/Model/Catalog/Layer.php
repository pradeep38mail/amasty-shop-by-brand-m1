<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_Catalog_Layer extends Mage_Catalog_Model_Layer
{
    /**
     * get all products without set root category as anchor
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @param int $categoryId
     */
    public function setProductCollection(Mage_Catalog_Model_Resource_Product_Collection $collection, $categoryId) {
        unset($this->_productCollections[$categoryId]);
        $this->_productCollections[$categoryId] = $collection;
    }
}
