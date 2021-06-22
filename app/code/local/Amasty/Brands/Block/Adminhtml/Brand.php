<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


/**
 * Class Brand
 *
 * @author Artem Brunevski
 */
class Amasty_Brands_Block_Adminhtml_Brand extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'ambrands';
        $this->_controller = 'adminhtml_brand';
        $this->_headerText = Mage::helper('ambrands')->__('Brands');
        parent::__construct();
        $this->_removeButton('add');
    }
}