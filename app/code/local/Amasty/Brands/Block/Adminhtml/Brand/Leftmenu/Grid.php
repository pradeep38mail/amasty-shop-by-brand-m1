<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Block_Adminhtml_Brand_Leftmenu_Grid extends Amasty_Brands_Block_Adminhtml_Brand_Base_Grid
{
    protected $_sideType = 'leftmenu';

    public function __construct()
    {
        parent::__construct();
        $this->setId('brand_leftmenu_grid');
        $this->setDefaultSort('leftmenu_position');
        $this->setDefaultFilter(array('show_in_leftmenu'  => 1));
        $this->_label = Mage::helper('ambrands')->__('Sidebar');
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     * @throws Mage_Core_Exception
     */
    protected function _prepareCollection()
    {
        $collection = $this->_prepareCommonCollection();
        $collection->addAttributeToSelect('leftmenu_position', 'left');
        $collection->addExpressionAttributeToSelect(
            'show_in_leftmenu',
            'IFNULL({{show_in_leftmenu}}, 0)',
            array('show_in_leftmenu'=>'show_in_leftmenu'));
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->_prepareSidebarColumns();
        return parent::_prepareColumns();
    }
}