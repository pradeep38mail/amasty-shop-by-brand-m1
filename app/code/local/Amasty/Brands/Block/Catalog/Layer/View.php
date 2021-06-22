<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


class Amasty_Brands_Block_Catalog_Layer_View extends Amasty_Brands_Block_Catalog_Layer_View_Pure
{
    /**
     * Get all fiterable attributes of current category
     *
     * @return array
     */
    protected function _getFilterableAttributes()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $attributes */
        $attributes = parent::_getFilterableAttributes();
        if (!$attributes || $this->getData('_ambrands_processed')) {
            return $attributes;
        }
        Mage::helper('ambrands')->removeBrandFilter($attributes);
        $this->setData('_filterable_attributes', $attributes);
        $this->setData('_ambrands_processed', 1);
        return $attributes;
    }

    public function _toHtml()
    {
        if ('true' == (string)Mage::getConfig()->getNode('modules/Amasty_Shopby/active')) {
            return '';
        }
        return parent::_toHtml();
    }
}
