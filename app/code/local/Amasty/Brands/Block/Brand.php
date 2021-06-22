<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Block_Brand extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $brandId = $this->getRequest()->getParam('ambrand_id', null);
        $storeId = Mage::app()->getStore()->getId();
        /** @var Amasty_Brands_Model_Brand  */
        $brand = Mage::getModel('ambrands/brand')->setStoreId($storeId)->load($brandId);
        $brand = $brand->getId() ? $brand : null;
        $this->setBrand($brand);
    }

    protected function _toHtml()
    {
        $res = parent::_toHtml();
        $brand = $this->getBrand();

        if (!$brand) {
            return $res;
        }

        $cmsId = $brand->getData('cms_block_id');
        $res = $this->getLayout()->createBlock('cms/block')->setBlockId($cmsId)->toHtml(). $res;
        $botCmsId = $brand->getData('bottom_cms_block_id');
        $res .= $this->getLayout()->createBlock('cms/block')->setBlockId($botCmsId)->toHtml();

        return $res;
    }

    public function getImageHtml()
    {
        $res = '';
        if (!$this->getBrand()) {
            return $res;
        }
        $brandUrl = $this->getBrand()->getImageUrl();
        if (!$brandUrl) {
            return $res;
        }
        $brandName = Mage::helper('core')->escapeHtml($this->getBrand()->getName());
        $res = '<p class="category-image"><img src="'
            . $brandUrl
            . '"  alt="' . $brandName
            . '" title="' . $brandName
            . '"/></p>';
        return $res;

    }
}
