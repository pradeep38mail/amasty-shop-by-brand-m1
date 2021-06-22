<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

/**
 * Class Image
 *
 * @author Artem Brunevski
 */
class Amasty_Brands_Block_Adminhtml_Brand_Entity_Helper_Image extends Varien_Data_Form_Element_Image
{
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $path =Mage::helper('ambrands')->getImageUrl($this->getHtmlId());
            $url = $path . $this->getValue();
        }
        return $url;
    }
}
