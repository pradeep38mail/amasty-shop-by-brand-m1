<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_System_Config_Source_Cmspages
{
    public function toOptionArray()
    {
        $result = array('' => Mage::helper('ambrands')->__("-Select CMS Page-"));
        $collection = Mage::getModel('cms/page')->getCollection();
        foreach ($collection as $page) {
           $result[$page->getId()] = $page->getTitle();
        }
        return $result;
    }
}
