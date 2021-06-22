<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_System_Config_Source_Toplinkposition
{
    public function toOptionArray()
    {
        $options = array(
            array(
                'label' => Mage::helper('ambrands')->__('Last'),
                'value' => Amasty_Brands_Model_Topmenu::CONFIG_SORTBY_LINK_POSITION_LAST
            ),
            array(
                'label' => Mage::helper('ambrands')->__('First'),
                'value' => Amasty_Brands_Model_Topmenu::CONFIG_SORTBY_LINK_POSITION_FIRST
            ),
            array(
                'label' => Mage::helper('ambrands')->__('Custom'),
                'value' => Amasty_Brands_Model_Topmenu::CONFIG_SORTBY_LINK_POSITION_CUSTOM
            ));
        return $options;
    }
}