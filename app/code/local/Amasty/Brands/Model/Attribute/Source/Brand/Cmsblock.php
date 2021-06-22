<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_Attribute_Source_Brand_Cmsblock
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
    implements Mage_Eav_Model_Entity_Attribute_Source_Interface
{
    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $cmsBlocks = Mage::getResourceModel('cms/block_collection')->load()->toOptionArray();
        array_unshift($cmsBlocks, array('value' => null, 'label' => Mage::helper('ambrands')->__('Please select a static block ...')));
        return $cmsBlocks;
    }
}