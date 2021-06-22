<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_Attribute_Backend_Image_Slider extends Amasty_Brands_Model_Attribute_Backend_Image
{
    protected $_subFolder = 'slider';

    protected function _imageBeforeLoad()
    {
        if (isset($_FILES['image_slider'])) {
            $fullName = $_FILES['image_slider']['tmp_name'];
            if ($fullName) {
                $height = Mage::getStoreConfig('ambrands/slider/image_height');
                $width = intval(Mage::getStoreConfig('ambrands/slider/image_width'));
                return $this->_resizeImage($fullName, $width, $height);
            }
        }

        return $this;
    }
}