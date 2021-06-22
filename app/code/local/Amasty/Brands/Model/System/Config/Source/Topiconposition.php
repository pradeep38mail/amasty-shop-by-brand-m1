<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_System_Config_Source_Topiconposition
{
    public function toOptionArray()
    {
        $options = array(
            array(
                'label' => Mage::helper('ambrands')->__('Left'),
                'value' => Amasty_Brands_Model_Topmenu::CONFIG_SORTBY_ICON_POSITION_LEFT
            ),
            array(
                'label' => Mage::helper('ambrands')->__('Right'),
                'value' => Amasty_Brands_Model_Topmenu::CONFIG_SORTBY_ICON_POSITION_RIGHT
            ));
        return $options;
    }
}