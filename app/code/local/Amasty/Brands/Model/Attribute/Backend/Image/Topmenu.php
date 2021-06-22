<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_Attribute_Backend_Image_Topmenu extends Amasty_Brands_Model_Attribute_Backend_Image
{
    protected $_subFolder = 'topmenu';

    protected function _imageBeforeLoad()
    {
        if (isset($_FILES['icon_topmenu'])) {
            $fullName = $_FILES['icon_topmenu']['tmp_name'];
            if ($fullName) {
                $height = Mage::getStoreConfig('ambrands/topmenu/icon_height');
                $width = intval(Mage::getStoreConfig('ambrands/topmenu/icon_width'));
                return $this->_resizeImage($fullName, $width, $height);
            }
        }
        return $this;
    }
}