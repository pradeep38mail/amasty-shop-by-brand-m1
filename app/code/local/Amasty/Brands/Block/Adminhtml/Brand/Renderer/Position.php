<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Block_Adminhtml_Brand_Renderer_Position extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Number
{
    public function _getInputValueElement(Varien_Object $row)
    {
        return  '<input type="text" class="input-text '
        . $this->getColumn()->getValidateClass()
        . '" name="' . $this->getColumn()->getId()
        . '[' .  $row->getId() . ']'
        . '" value="' . $this->_getInputValue($row) . '"/>';
    }
}