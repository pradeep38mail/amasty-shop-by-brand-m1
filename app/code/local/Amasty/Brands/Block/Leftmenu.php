<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Block_Leftmenu extends Mage_Core_Block_Template
{
    protected $_items;
    protected $_itemsNumber;
    protected $_showIcons;
    protected $_sortBy;

    public function __construct()
    {
        $this->_itemsNumber =  $this->_getConfig('items_num');
        $this->_showIcons= $this->_getConfig('icon');
        $this->_sortBy =$this->_getConfig('sort');
        if ($this->_sortBy == 'position') {
            $this->_sortBy = 'leftmenu_position';
        }
        $this->setIconWidth ($this->_getConfig('icon_width'));
        $this->setIconHeight($this->_getConfig('icon_height'));
        $this->setShowSearch($this->_getConfig('show_search'));
        $this->setIconClass (' leftmenu_brandicon');
        if ( $this->_getConfig('icon_position') == 'right') {
            $this->setIconClass($this->getIconClass() . ' right');
        }
    }

    /**
     * @return Amasty_Brands_Model_Resource_Brand_Collection
     */
    public function getItems()
    {
        if (!$this->_items) {
            /** @var Amasty_Brands_Model_Resource_Brand_Collection $items */
            $brands = Mage::getModel('ambrands/brand')
                ->getCollection()
                ->setStoreId(Mage::app()->getStore()->getId())
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('icon_leftmenu')
                ->addAttributeToSelect('image')
                ->addAttributeToFilter('show_in_leftmenu', '1')
                ->addAttributeToSort($this->_sortBy)
                ->addAttributeToFilter('is_active', '1')
                ->setPageSize($this->_itemsNumber)
                ->setCurPage(1);

            $this->_items = $brands;
        }

        return $this->_items;
    }

    public function getBrandLabelUrl(Amasty_Brands_Model_Brand $brand)
    {
        $label = '<a href="' .  $brand->getUrl()
            .'">' . $brand->getName() . '</a>';
        $imgPath = $this->_getImgPath($brand);
        if ($this->_showIcons && $imgPath) {
            $label = '<img class="' . $this->getIconClass() . '" src="'
                . $imgPath . '"/>'
                . '<span>' . $label . '</span>';
        }
        return $label;
    }

    /**
     * @param Amasty_Brands_Model_Brand $brand
     * @return string
     */
    protected function _getImgPath(Amasty_Brands_Model_Brand $brand)
    {
        if ($brand->getIconLeftmenu())
            return Mage::helper('ambrands')->getImageUrl('leftmenu') . $brand->getIconLeftmenu();
        if ($brand->getImage())
            return $brand->getImageUrl();
        return '';
    }

    /**
     * @param string $node
     * @return string
     */
    protected function _getConfig($node)
    {
        return Mage::getStoreConfig('ambrands/leftmenu/' . $node);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->_getConfig('enabled');
    }

    protected function _toHtml()
    {
        $res = '';
        $brandId = Mage::app()->getRequest()->getParam('ambrand_id', null);
        $showOnBrandPage = (bool) Mage::getStoreConfig('ambrands/brand_page/show_leftmenu') || !$brandId;
        if ($this->isEnabled() && $showOnBrandPage && $this->getItems()->getSize()) {
            $res = parent::_toHtml();
        }
        $res .= $this->getSearchHtml();
        return $res;
    }

    /**
     * @return string
     */
    public function getSearchHtml()
    {
        $res = '';
        if (!$this->getShowSearch()) {
            return $res;
        }
        $block = $this->getLayout()->createBlock('ambrands/search', 'ambrands.search')
            ->setData('side', true)
            ->setTemplate('amasty/ambrands/search.phtml');
        return $block->toHtml();
    }

}
