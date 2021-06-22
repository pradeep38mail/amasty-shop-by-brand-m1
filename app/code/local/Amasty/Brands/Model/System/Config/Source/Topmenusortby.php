<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_System_Config_Source_Topmenusortby
{
    public function toOptionArray()
    {
        $options = array(
            array(
                'label' => Mage::helper('ambrands')->__('Position'),
                'value' => Amasty_Brands_Model_Topmenu::CONFIG_SORTBY_POSITION
            ),
            array(
                'label' => Mage::helper('ambrands')->__('Name'),
                'value' => Amasty_Brands_Model_Topmenu::CONFIG_SORTBY_NAME
            ));
        return $options;
    }
}