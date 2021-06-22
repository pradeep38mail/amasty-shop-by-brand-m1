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
class Amasty_Brands_Model_Observer_Catalog_Product_Attribute
{
    /**
     * After product attribute save
     * @param Varien_Event_Observer $observer
     */
    public function onSaveAfter(Varien_Event_Observer $observer)
    {
        $dataObject = $observer->getDataObject();
        if ($dataObject instanceof Mage_Catalog_Model_Resource_Eav_Attribute) {
            if ($dataObject->getAttributeCode() === Mage::helper('ambrands')->getBrandAttributeCode()) {
                /** @var Amasty_Brands_Model_Mapper $mapper */
                $mapper = Mage::getSingleton('ambrands/mapper');

                Mage::getModel('ambrands/brand')->deleteOptions(
                    array_keys(array_diff($dataObject->getOption()['delete'], array('')))
                );

                $mapper->run();
                $mapper->assignProductsFromAttribute();
            }
        }
    }
}