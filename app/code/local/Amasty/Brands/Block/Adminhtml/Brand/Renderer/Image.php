<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Block_Adminhtml_Brand_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $img = $row->getData($this->getColumn()->getIndex());
        if (!$img) {
            return '';
        }
        $val = Mage::helper('ambrands')->getImageUrl() . $img;
        $out = "<img src=". $val ." width='97px'/>";
        return $out;
    }
}