<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_System_Config_Source_Allbrandsimagetype
{
    public function toOptionArray()
    {
        $options = array(
            array(
                'label' => Mage::helper('ambrands')->__('Icon'),
                'value' => Amasty_Brands_Block_List::CONFIG_IMAGE_TYPE_ICON
            ),
            array(
                'label' => Mage::helper('ambrands')->__('Big Image'),
                'value' => Amasty_Brands_Block_List::CONFIG_IMAGE_TYPE_BIG
            )
        );
        return $options;
    }
}