<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_Config extends Varien_Object
{
    /** @var  Mage_Eav_Model_Entity */
    protected $_attribute;

    /**
     * @return Mage_Eav_Model_Entity_Attribute
     * @throws Mage_Core_Exception
     */
    public function getBrandAttribute()
    {
        if (!$this->_attribute) {
            $attributeCode = Mage::helper('ambrands')->getBrandAttributeCode();

            $attributeModel = Mage::getModel('eav/entity_attribute')
                ->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);

            if (!$attributeModel->getId() && $attributeModel->usesSource()) {
                Mage::throwException(Mage::helper('ambrands')->
                __('Wrong brands attribute.')
                );
            }

            if (!$attributeModel->getSourceModel()) {
                $attributeModel->setSourceModel('eav/entity_attribute_source_table');
            }

            $this->_attribute = $attributeModel;
        }

        return $this->_attribute;
    }

    /**
     * @param bool|true $withEmpty
     * @param bool|false $defaultValues
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getBrandAttributeOptions($withEmpty = true, $defaultValues = false)
    {
        return $this->getBrandAttribute()->getSource()->getAllOptions($withEmpty, $defaultValues);
    }
}