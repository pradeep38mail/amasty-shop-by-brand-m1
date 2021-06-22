<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_Attribute_Backend_Image_Leftmenu extends Amasty_Brands_Model_Attribute_Backend_Image
{
    protected $_subFolder = 'leftmenu';

    protected function _imageBeforeLoad()
    {
        if (isset($_FILES['icon_leftmenu'])) {
            $fullName = $_FILES['icon_leftmenu']['tmp_name'];
            if ($fullName) {
                $height = Mage::getStoreConfig('ambrands/leftmenu/icon_height');
                $width = intval(Mage::getStoreConfig('ambrands/leftmenu/icon_width'));
                return $this->_resizeImage($fullName, $width, $height);
            }
        }
        return $this;
    }
}