<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Block_Adminhtml_Brand_Slider_Grid extends Amasty_Brands_Block_Adminhtml_Brand_Base_Grid
{
    protected $_sideType = 'slider';

    public function __construct()
    {
        parent::__construct();
        $this->setId('brand_slider_grid');
        $this->setDefaultSort('slider_position');
        $this->setDefaultFilter(array('show_in_slider'  => 1));
        $this->_label = Mage::helper('ambrands')->__('Slider');
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     * @throws Mage_Core_Exception
     */
    protected function _prepareCollection()
    {
        $collection = $this->_prepareCommonCollection();
        $collection->addAttributeToSelect('slider_position', 'left');
        $collection->addExpressionAttributeToSelect(
            'show_in_slider',
            'IFNULL({{show_in_slider}}, 0)',
            array('show_in_slider'=>'show_in_slider'));
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->_prepareSidebarColumns();
        return parent::_prepareColumns();
    }
}