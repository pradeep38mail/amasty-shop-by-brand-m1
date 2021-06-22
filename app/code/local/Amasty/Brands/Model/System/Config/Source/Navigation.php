<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_System_Config_Source_Navigation
{
    public function toOptionArray()
    {
        $options = array(
            array(
                'label' => Mage::helper('adminhtml')->__('Yes'),
                'value' => Amasty_Brands_Model_Brand::CONFIG_NAVIGATION_DISPLAY
            ),
            array(
                'label' => Mage::helper('adminhtml')->__('No'),
                'value' => Amasty_Brands_Model_Brand::CONFIG_NAVIGATION_HIDE
            ),/** @todo
            array(
                'label' => Mage::helper('ambrands')->__('Categories Only'),
                'value' => '2'
            ), */
        );
        return $options;
    }
}