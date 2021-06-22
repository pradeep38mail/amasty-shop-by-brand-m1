<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_System_Config_Source_Allbrandsdisplay
{
    public function toOptionArray()
    {
        $options = array(
            array(
                'label' => Mage::helper('ambrands')->__('Horizontal'),
                'value' => Amasty_Brands_Block_List::CONFIG_DISPLAY_HORIZONTAL
            ),
            array(
                'label' => Mage::helper('ambrands')->__('Vertical'),
                'value' => Amasty_Brands_Block_List::CONFIG_DISPLAY_VERTICAL
            )
        );
        return $options;
    }
}