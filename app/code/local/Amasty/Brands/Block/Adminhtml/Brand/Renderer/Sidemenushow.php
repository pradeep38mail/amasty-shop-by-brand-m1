<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Block_Adminhtml_Brand_Renderer_Sidemenushow extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
    public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        return $value === NULL ? Mage::helper('adminhtml')->__('No') : parent::render($row) ;
    }
}