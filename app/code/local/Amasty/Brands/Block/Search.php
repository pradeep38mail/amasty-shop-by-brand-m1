<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Block_Search extends Mage_Core_Block_Template
{
    /**
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getBrands()
    {
        $brandCollection = Mage::getModel('ambrands/brand')
            ->getCollection()
            ->addFieldToFilter('is_active', '1')
            ->addAttributeToSelect('name');

        $optionsCounts = array();
        $hideBrandsWithoutProducts = !Mage::getStoreConfig('ambrands/brands_landing/display_zero');
        if ($hideBrandsWithoutProducts) {
            $optionsCounts = Mage::helper('ambrands')->getBrandOptionsCounts();
        }

        $res = array();
        foreach ($brandCollection as $brand) {
            /** @var Amasty_Brands_Model_Brand $brand */
            if ($hideBrandsWithoutProducts) {
                $productsCount = isset($optionsCounts[$brand->getOptionId()]) ? $optionsCounts[$brand->getOptionId()] : '0';
                if (!$productsCount) {
                    continue;
                }
            }

            $url = $brand->getUrl();
            $res[$url] = $brand->getName();
        }

        return json_encode($res);
    }
}
