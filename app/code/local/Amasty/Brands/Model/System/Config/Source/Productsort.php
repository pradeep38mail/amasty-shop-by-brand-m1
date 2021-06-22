<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_System_Config_Source_Productsort
{
    public function toOptionArray()
    {
        return Mage::getModel('catalog/config')->getAttributeUsedForSortByArray();
    }
}