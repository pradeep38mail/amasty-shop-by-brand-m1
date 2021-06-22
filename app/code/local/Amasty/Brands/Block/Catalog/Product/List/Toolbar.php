<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


class Amasty_Brands_Block_Catalog_Product_List_Toolbar extends Amasty_Brands_Block_Catalog_Product_List_Toolbar_Pure
{
    public function getPagerUrl($params=array())
    {
        $brandId = $this->getRequest()->getParam('ambrand_id', null);
        $brand = Mage::getModel('ambrands/brand')->load($brandId);
        $res = parent::getPagerUrl($params);
        if (!$brand->getId()) {
            return $res;
        }
        return Mage::helper('ambrands')->rebuildUrl($res, $brand->getUrlKey());
    }

    public function setDefaultOrder($ignoredField)
    {
        $field = Mage::getStoreConfig('ambrands/brand_page/sort');
        if (isset($this->_availableOrder[$field])) {
            $this->_orderField = $field;
        }
        return $this;
    }
}