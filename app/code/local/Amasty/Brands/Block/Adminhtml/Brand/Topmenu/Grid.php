<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Block_Adminhtml_Brand_Topmenu_Grid extends Amasty_Brands_Block_Adminhtml_Brand_Base_Grid
{
    protected $_sideType = 'topmenu';

    public function __construct()
    {
        parent::__construct();
        $this->setId('brand_topmenu_grid');
        $this->setDefaultSort('topmenu_position');
        $this->setDefaultFilter(array('show_in_topmenu'  => 1));
        $this->_label = Mage::helper('ambrands')->__('Top Menu');
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     * @throws Mage_Core_Exception
     */
    protected function _prepareCollection()
    {
        $collection = $this->_prepareCommonCollection();
        $collection->addAttributeToSelect('topmenu_position', 'left');
        $collection->addExpressionAttributeToSelect(
            'show_in_topmenu',
            'IFNULL({{show_in_topmenu}}, 0)',
            array('show_in_topmenu'=>'show_in_topmenu'));
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->_prepareSidebarColumns();
        return parent::_prepareColumns();
    }
}