<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

/**
 * Class Amasty_Brands_Block_List
 *
 * @method Mage_Core_Model_Variable setShowScrollbar(string $value)
 * @method string getShowScrollbar()
 * @method Mage_Core_Model_Variable setShowImages(string $value);
 * @method string getShowImages()
 * @method Mage_Core_Model_Variable setImageType(string $value)
 * @method string getImageType()
 * @method Mage_Core_Model_Variable setShowCounts(string $value)
 * @method string getShowCounts()
 * @method Mage_Core_Model_Variable setDisplayAllBrands(string $value)
 * @method string getDisplayAllBrands()
 * @method Mage_Core_Model_Variable setDisplayType(string $value)
 * @method string getDisplayType()
 * @method Mage_Core_Model_Variable setItemsPerLine(string $value)
 * @method string getItemsPerLine()
 * @method Mage_Core_Model_Variable setColumnNumber(string $value)
 * @method string getColumnNumber()
 * @method Mage_Core_Model_Variable setShowLetters(string $value)
 * @method string getShowLetters()
 * @method Mage_Core_Model_Variable setShowSearch(string $value)
 * @method string getShowSearch()
 * @method Mage_Core_Model_Variable setShowFilter(string $value)
 * @method string getShowFilter()
 * @method Mage_Core_Model_Variable setFilterDisplayAll(string $value)
 * @method string getFilterDisplayAll()
 * @method Mage_Core_Model_Variable setImageWidth(string $value)
 * @method string getImageWidth()
 * @method Mage_Core_Model_Variable setImageHeight(string $value)
 * @method string getImageHeight()
 */
class Amasty_Brands_Block_List extends Mage_Core_Block_Template
{
    const CONFIG_DISPLAY_HORIZONTAL = 'horizontal';
    const CONFIG_DISPLAY_VERTICAL   = 'vertical';
    const CONFIG_IMAGE_TYPE_ICON    = 'icon';
    const CONFIG_IMAGE_TYPE_BIG     = 'big';

    /**
     * @var array
     */
    protected $_items = array();

    public function __construct(array $args)
    {
        parent::__construct($args);
        $this->setShowImages(       (bool)$this->_getConfig('show_images'));
        $this->setImageType(        $this->_getConfig('image_type'));
        $this->setShowCounts(       (bool)$this->_getConfig('show_count'));
        $this->setDisplayAllBrands( (bool)$this->_getConfig('display_zero'));
        $this->setDisplayType(      $this->_getConfig('display_type'));
        $this->setItemsPerLine(     max(1, $this->_getConfig('items_per_line')));
        $this->setColumnNumber(     max(1, $this->_getConfig('columns_num')));
        $this->setShowLetters(      (bool)$this->_getConfig('show_letters'));
        $this->setShowSearch(       (bool)$this->_getConfig('show_search'));
        $this->setShowFilter(       (bool)$this->_getConfig('show_filter'));
        $this->setFilterDisplayAll( (bool)$this->_getConfig('filter_display_all'));
        $this->setImageWidth (      $this->_getConfig('image_width'));
        $this->setImageHeight(      $this->_getConfig('image_height'));
    }
    /**
     * @return Mage_Core_Block_Abstract
     * @throws Mage_Core_Exception
     */
    protected function _prepareLayout()
    {
        $attribute = Mage::helper('ambrands')->getBrandAttribute();
        if (!$attribute->getId()){
            return parent::_prepareLayout();
        }

        $optionsCounts = Mage::helper('ambrands')->getBrandOptionsCounts();

        $options = $attribute->getFrontend()->getSelectOptions();
        array_shift($options);

        $this->_removeDisabledBrands($options);

        $optionIds = array();
        foreach ($options as $opt){
            $optionIds[] = $opt['value'];
        }
        $brandCollection = Mage::getModel('ambrands/brand')
            ->getCollection()
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addFieldToFilter('option_id', array('in'=>$optionIds))
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('is_active', '1');

        $images = $this->getShowImages() ? $this->_getOptionImages($brandCollection) : null;
        $urls = $this->_getOptionUrls($brandCollection);
        $titles = $this->_getOptionTitles($brandCollection);

        $c = 0;
        $letters = array();
        foreach ($options as $opt){
            if (!isset($titles[$opt['value']])) {
                continue;
            }
            $opt['cnt'] = isset($optionsCounts[$opt['value']]) ? $optionsCounts[$opt['value']] : '0';
            if (!$this->getDisplayAllBrands() && !$opt['cnt']) {
                continue;
            }
            $opt['url'] = $urls[$opt['value']];
            $opt['img'] = $images ? $images[$opt['value']] : null;
            $opt['label'] = $titles[$opt['value']];


            if (function_exists('mb_strtoupper')) {
                $i = mb_strtoupper(mb_substr($opt['label'], 0, 1, 'UTF-8'));
            } else {
                $i = strtoupper(substr($opt['label'], 0, 1));
            }

            if (is_numeric($i)) { $i = '0-9'; }

            if (!isset($letters[$i]['items'])){
                $letters[$i]['items'] = array();
            }

            $letters[$i]['items'][] = $opt;

            if (!isset($letters[$i]['count'])){
                $letters[$i]['count'] = 0;
            }

            $letters[$i]['count']++;

            ++$c;
            }
        if (!$letters){
            return parent::_prepareLayout();
        }
        uksort($letters, array($this, '_sortByName'));

        $itemsNum = $this->getShowLetters() ? $c + sizeof($letters) : $c;
        $columns = $this->getDisplayType() == self::CONFIG_DISPLAY_VERTICAL
            ? abs(intVal($this->getColumnNumber())) : 1;

        $itemsPerColumn = ceil(($itemsNum) / max(1, $columns));
        $col = 0; // current column
        $num = 0; // current number of items in column
        foreach ($letters as $letter => $items){
            $this->_items[$col][$letter] = $items['items'];
            $num += $items['count'];
            if ($this->getShowLetters()) {
                $num++;
            }
            if ($num >= $itemsPerColumn){
                $num = 0;
                $col++;
            }
        }
        
        return parent::_prepareLayout();
    }

    /**
     * @param Amasty_Brands_Model_Resource_Brand_Collection $brands
     * @return array
     */
    protected function _getOptionImages($brands)
    {
        $images = array();
        foreach ($brands as $brand){
            $images[$brand->getOptionId()] = $brand->getImage()
                ? $brand->getImageUrl()
                : null;
        }
        return $images;
    }

    /**
     * @param $items
     * @return array
     */
    protected function _getOptionTitles($items)
    {
        $titles = array();
        foreach ($items as $value){
            $titles[$value->getOptionId()] = $value->getName();
        }
        return $titles;
    }

    /**
     * @param $items
     * @return array
     */
    protected function _getOptionUrls($items)
    {
        $urls = array();
        foreach ($items as $value){
            $urls[$value->getOptionId()] = $value->getUrl();
        }
        return $urls;
    }

    /**
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getSearchBrands()
    {
        $brandCollection = Mage::getModel('ambrands/brand')
            ->getCollection()
            ->addFieldToFilter('is_active', '1')
            ->addAttributeToSelect('name');
        $res = array();
        foreach ($brandCollection as $brand) {
            $url = $brand->getUrl();
            $res[$url] = $brand->getName();
        }
        return $res;
    }

    public function getItems()
    {
        return $this->_items;
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    public function _sortByName($a, $b)
    {
        $a = trim($a);
        $b = trim($b);

        if ($a == '') return 1;
        if ($b == '') return -1;

        $x = substr($a, 0, 1);
        $y = substr($b, 0, 1);
        if (is_numeric($x) && !is_numeric($y)) return 1;
        if (!is_numeric($x) && is_numeric($y)) return -1;

        if (function_exists('mb_strtoupper')) {
            $res = strcmp(mb_strtoupper($a), mb_strtoupper($b));
        } else {
            $res = strcmp(strtoupper($a), strtoupper($b));
        }
        return $res;
    }

    /**
     * @param $options
     * @return $this
     */
    protected function _removeDisabledBrands(&$options)
    {
        $brands = Mage::getModel('ambrands/brand')
            ->getCollection()
            ->addAttributeToFilter('is_active', '0');
        $brandOptions = array();
        foreach ($brands as $brand) {
            $brandOptions[] = $brand->getOptionId();
        }
        foreach ($options as $key => $opt) {
            if (in_array($opt['value'], $brandOptions)) {
                unset($options[$key]);
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getAllLetters()
    {
        $res = array();
        foreach ($this->_items as $columns) {
            $res = array_merge($res, array_keys($columns));
        }
        return $res;
    }

    /**
     * @param string $node
     * @return string
     */
    protected function _getConfig($node)
    {
        return Mage::getStoreConfig('ambrands/brands_landing/' . $node);
    }

    /**
     * @return string
     */
    public function getItemMinWidth()
    {
        $items = intval($this->getItemsPerLine()) ? intval($this->getItemsPerLine()) : 5;
        $percent = 100.0 / $items;
        return "calc($percent% - 5px)";
    }

    /**
     * @return string
     */
    public function getColumnSeparatorWidth()
    {
        $width = floor(100/max(1, count($this->_items)));
        return "calc($width% - 10px)";
    }

    /**
     * @return string
     */
    public function getSearchHtml()
    {
        $res = '';
        if (!$this->getShowSearch   ()) {
            return $res;
        }
        $block = $this->getLayout()->createBlock('ambrands/search', 'ambrands.search')
            ->setTemplate('amasty/ambrands/search.phtml');
        return $block->toHtml();
    }


}
