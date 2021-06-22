<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_Catalog_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item
{
    public function getUrl()
    {
        $brandId = Mage::app()->getRequest()->getParam('ambrand_id', null);
        $brand = Mage::getModel('ambrands/brand')->load($brandId);
        $res = parent::getUrl();
        if (!$brand->getId()) {
            return $res;
        }
        return Mage::helper('ambrands')->rebuildUrl($res, $brand->getUrlKey());
    }

    public function getRemoveUrl()
    {
        $brandId = Mage::app()->getRequest()->getParam('ambrand_id', null);
        $brand = Mage::getModel('ambrands/brand')->load($brandId);
        $res = parent::getRemoveUrl();
        if (!$brand->getId()) {
            return $res;
        }
        return Mage::helper('ambrands')->rebuildUrl($res, $brand->getUrlKey());
    }

}