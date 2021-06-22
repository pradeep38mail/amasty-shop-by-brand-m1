<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


/**
 * Class Attribute
 *
 * @author Artem Brunevski
 */
class Amasty_Brands_Model_System_Config_Source_Attribute
{
    public function toOptionArray()
    {
        $options = array(
            array(
                'value' => '',
                'label' => Mage::helper('ambrands')->__('--None--')
            )
        );

        /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $collection */
        $collection = Mage::getResourceModel('catalog/product_attribute_collection');
        $collection
            ->addFieldToFilter('frontend_input', array('select'))
            ->addIsFilterableFilter();

        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        foreach($collection as $attribute){
            $options[] = array(
                'label' => $attribute->getFrontendLabel(),
                'value' => $attribute->getAttributeCode()
            );
        }
        return $options;
    }
}