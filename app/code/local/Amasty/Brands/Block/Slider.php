<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

/**
 * Class Amasty_Brands_Block_Slider
 *
 * @method Mage_Core_Model_Variable setShowPagination(string $value)
 * @method string getShowPagination()
 * @method Mage_Core_Model_Variable setShowButtons(string $value)
 * @method string getShowButtons()
 * @method Mage_Core_Model_Variable setSliderWidth(string $value)
 * @method string getSliderWidth()
 * @method Mage_Core_Model_Variable setShowLabel(string $value)
 * @method string getShowLabel()
 * @method Mage_Core_Model_Variable setItemsNumber(string $value)
 * @method string getItemsNumber()
 * @method Mage_Core_Model_Variable setPagintaionIsClicable(string $value)
 * @method string getPagintaionIsClicable()
 * @method Mage_Core_Model_Variable setAutoplay(string $value)
 * @method string getAutoplay()
 * @method Mage_Core_Model_Variable setAutoplayDelay(string $value)
 * @method string getAutoplayDelay()
 * @method Mage_Core_Model_Variable setInfinityLoop(string $value)
 * @method string getInfinityLoop()
 * @method Mage_Core_Model_Variable setSimulateTouch(string $value)
 * @method string getSimulateTouch()
 * @method Mage_Core_Model_Variable setImageWidth(string $value)
 * @method string getImageWidth() 
 * @method Mage_Core_Model_Variable setImageHeight(string $value)
 * @method string getImageHeight()
 */
class Amasty_Brands_Block_Slider extends Mage_Core_Block_Template
{
    const DEFAULT_IMG_WIDTH = 130;
    protected $_items;

    protected function _construct()
    {
        parent::_construct();
        $this->setShowPagination(       (bool)$this->_getConfig('pagination'));
        $this->setShowButtons(          (bool)$this->_getConfig('buttons'));
        $this->setSliderWidth(          $this->_getConfig('width'));
        $this->setShowLabel(            $this->_getConfig('labels'));
        $this->setItemsNumber(          max(1,$this->_getConfig('items_num')));
        $this->setPagintaionIsClicable( $this->_getConfig('pagination_clickable'));
        $this->setAutoplay(             (bool)$this->_getConfig('autoplay'));
        $this->setAutoplayDelay(        $this->_getConfig('autoplay_delay'));
        $this->setInfinityLoop(         $this->_getConfig('infinity'));
        $this->setSimulateTouch(        $this->_getConfig('simulate_touch'));
        $this->setImageWidth (           $this->_getConfig('image_width'));
        $this->setImageHeight(           $this->_getConfig('image_height'));

        $sortBy = $this->_getConfig('sort');
        if ($sortBy == 'position') {
            $sortBy = 'slider_position';
        }
        $this->setSortBy($sortBy);
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
                ->addAttributeToSelect('image_slider')
                ->addAttributeToSelect('image')
                ->addAttributeToSort($this->getSortBy())
                ->addAttributeToFilter('show_in_slider', '1')
                ->addAttributeToFilter('is_active', '1')
                ->addAttributeToFilter(array(
                    array('attribute'=> 'image_slider','neq' => ''),
                    array('attribute'=> 'image','neq' => ''),
                ), null, 'left');

            $this->_items = $brands;
            $this->setItemsNumber(min($this->getItemsNumber(), $this->_items->getSize()));
        }

        return $this->_items;
    }

    /**
     * @return array
     */
    public function getSliderOptions()
    {
        $options = array();
        $options['slidesPerView']   = $this->getItemsNumber();
        $options['loop']            = $this->getInfinityLoop()  ? 'true' : 'false';
        $options['simulateTouch']   = $this->getSimulateTouch()  ? 'true' : 'false';
        if($this->getShowPagination()) {
            $options['pagination'] = '".swiper-pagination"';
            $options['paginationClickable'] = $this->getPagintaionIsClicable() ? 'true' : 'false';
        }
        if($this->getAutoplay()) {
            $options['autoplay'] = intval($this->getAutoplayDelay());
        }
        return $options;
    }

    /**
     * @param Amasty_Brands_Model_Brand $brand
     * @return string
     */
    public function getImgPath(Amasty_Brands_Model_Brand $brand)
    {
        if ($brand->getImageSlider())
            return Mage::helper('ambrands')->getImageUrl('slider') . $brand->getImageSlider();
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
        return Mage::getStoreConfig('ambrands/slider/' . $node);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->_getConfig('enabled');
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->isEnabled() || !$this->getItems()->getSize()) {
            return '';
        }
        return parent::_toHtml();
    }
}
