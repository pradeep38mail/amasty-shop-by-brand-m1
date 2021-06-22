<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_Topmenu
{
    const IMAGE_TAG = 'brand_image_tag';
    const IMAGE_TAG_END = 'brand_end_image_tag';
    
    const CONFIG_SORTBY_POSITION = 'position';
    const CONFIG_SORTBY_NAME = 'name';
    const CONFIG_SORTBY_LINK_POSITION_FIRST = 'first';
    const CONFIG_SORTBY_LINK_POSITION_LAST = 'last';
    const CONFIG_SORTBY_LINK_POSITION_CUSTOM = 'custom';
    const CONFIG_SORTBY_ICON_POSITION_LEFT = 'left';
    const CONFIG_SORTBY_ICON_POSITION_RIGHT = 'right';

    /**
     * @var Varien_Data_Tree_Node
     */
    protected $_menu;
    /**
     * @var string
     */
    protected $_enabled;
    /**
     * @var string
     */
    protected $_itemsNumber;
    /**
     * @var string
     */
    protected $_brandsLinkPos;
    /**
     * @var string
     */
    protected $_sortBy;
    /**
     * @var string
     */
    protected $_displayIcon;
    /**
     * @var string
     */
    protected $_iconPos;
    /**
     * @var string
     */
    protected $_iconWidth;
    /**
     * @var string
     */
    protected $_iconHeight;

    public function __construct($menu)
    {
        $this->_menu = $menu;
        $this->_enabled = Mage::getStoreConfig('ambrands/topmenu/enabled');
        $this->_itemsNumber = Mage::getStoreConfig('ambrands/topmenu/items_num');
        $this->_sortBy = Mage::getStoreConfig('ambrands/topmenu/sort');
        $this->_displayIcon = Mage::getStoreConfig('ambrands/topmenu/icon');
        $this->_iconPos = Mage::getStoreConfig('ambrands/topmenu/icon_position');
        $this->_iconWidth = Mage::getStoreConfig('ambrands/topmenu/icon_width');
        $this->_iconHeight = Mage::getStoreConfig('ambrands/topmenu/icon_height');
        $this->_brandsLinkPos = Mage::getStoreConfig('ambrands/topmenu/position');
        if ($this->_sortBy == 'position') {
            $this->_sortBy = 'topmenu_position';
        }
    }

    /**
     * @return Amasty_Brands_Model_Resource_Brand_Collection
     */
    public function getItems()
    {
        $brands = Mage::getModel('ambrands/brand')
            ->getCollection()
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('icon_topmenu')
            ->addAttributeToSelect('image')
            ->addAttributeToFilter('show_in_topmenu', '1')
            ->addAttributeToSort($this->_sortBy)
            ->addAttributeToFilter('is_active', '1')    
            ->setPageSize($this->_itemsNumber)
            ->setCurPage(1);
        return $brands;
    }

    /**
     * @return bool
     */
    public function addBrands()
    {
        if (!$this->_enabled || !$this->_menu) {
            return false;
        }
        try {
            $brands = $this->getItems();

            $node = $this->_addBrandsLink();

            $tree = $node->getTree();
            foreach ($brands as $brand) {
                $data = array(
                    'name' => $this->getBrandLabelUrl($brand),
                    'id' => $brand->getId(),
                    'url' => $brand->getUrl(),
                    'class' => 'amtopmenu-container',
                );

                $subNode = new Varien_Data_Tree_Node($data, 'id', $tree, $node);
                $node->addChild($subNode);
            }
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }
        return true;
    }

    /**
     * @return Varien_Data_Tree_Node
     */
    protected function _addBrandsLink()
    {
        $tree = $this->_menu->getTree();

        $brandsNode = new Varien_Data_Tree_Node(array(
            'name'  => Mage::helper('ambrands')->__('Brands'),
            'id'    => 'category-node-brands',
            'url'   => Mage::helper('ambrands')->getBrandsPageUrl(),
        ), 'id', $tree, $this->_menu);


        $position = 1;
        if ($this->_brandsLinkPos == self::CONFIG_SORTBY_LINK_POSITION_CUSTOM) {
            $position =  Mage::getStoreConfig('ambrands/topmenu/custom_position');
        }
        $removedNodes = array();
        if ($this->_brandsLinkPos == self::CONFIG_SORTBY_LINK_POSITION_LAST
            || $position > $this->_menu->getChildren()->count())
        {
            $this->_menu->addChild($brandsNode);
        }
        else {
            foreach ($this->_menu->getChildren() as $node) {
                if (--$position > 0)
                    continue;
                $removedNodes[] = $node;
                $tree->removeNode($node);
            }

            $this->_menu->addChild($brandsNode);

            foreach ($removedNodes as $node) {
                $this->_menu->addChild($node);
            }
        }

        return $brandsNode;
    }

    /**
     * @param Amasty_Brands_Model_Brand $brand
     * @return string
     */
    public function getBrandLabelUrl(Amasty_Brands_Model_Brand $brand)
    {
        $label = $brand->getName();
        $imgUrl = $this->_getImgUrl($brand);
        if ($this->_displayIcon && $imgUrl) {
            $image = self::IMAGE_TAG
                . ' class=amtopmenu-icon'
                . $imgUrl
                . self::IMAGE_TAG_END;
            if ($this->_iconPos == self::CONFIG_SORTBY_ICON_POSITION_LEFT) {
                $label = $image . $label;
            } else {
                $image = str_replace('amtopmenu-icon', 'amtopmenu-icon-right', $image);
                $label .= $image;
            }
        }
        return $label;
    }

    /**
     * @param Amasty_Brands_Model_Brand $brand
     * @return string
     */
    protected function _getImgUrl(Amasty_Brands_Model_Brand $brand)
    {
        $path = '';
        if ($brand->getIconTopmenu())
            $path =  Mage::helper('ambrands')->getImageUrl('topmenu') . $brand->getIconTopmenu();
        if ($brand->getImage())
            $path = $brand->getImageUrl();
        if (!$path) {
            return '';
        }
        $res = '';
        $width  = max(0, intval(Mage::getStoreConfig('ambrands/topmenu/icon_width')));
        $height = max(0, intval(Mage::getStoreConfig('ambrands/topmenu/icon_height')));
        if ($width) {
            $res .= 'max-width:' . $width . 'px;';
        }
        if ($height) {
            $res .= 'max-height:' . $height . 'px;';
        }
        if ($res) {
            $res = ' style=' . $res;
        }
        $res .= ' src=' . $path;
        return $res;
    }

}
