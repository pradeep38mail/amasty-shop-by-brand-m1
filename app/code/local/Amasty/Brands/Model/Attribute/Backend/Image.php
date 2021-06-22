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
class Amasty_Brands_Model_Attribute_Backend_Image extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    protected $_subFolder = '';

    /**
     * Save uploaded file and set its name to brand
     * @param Varien_Object $object
     * @throws Exception
     * @return $this
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getName());

        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
            return $this;
        }

        $path = $this->_getPath();
        try {
            $this->_imageBeforeLoad();
            $uploader = new Mage_Core_Model_File_Uploader($this->getAttribute()->getName());
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($path);


            $object->setData($this->getAttribute()->getName(), $result['file']);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
        } catch (Exception $e) {
            if ($e->getCode() != Mage_Core_Model_File_Uploader::TMP_NAME_EMPTY) {
                Mage::logException($e);
            }
            return $this;
        }
    }

    protected function _getPath()
    {
        return Mage::helper('ambrands')->getImageFolderPath($this->_subFolder);
    }

    protected function _imageBeforeLoad()
    {
        return $this;
    }

    protected function _resizeImage($path, $width, $height)
    {
        if ($width) {
            $height = $height ? intval($height) : null;
            $imageObj = new Varien_Image($path);
            $imageObj->constrainOnly(true);
            $imageObj->keepTransparency(true);
            $imageObj->keepFrame(false);
            $imageObj->keepAspectRatio(true);
            $imageObj->resize($width, $height);
            $imageObj->save($path);
        }
        return $this;
    }


}