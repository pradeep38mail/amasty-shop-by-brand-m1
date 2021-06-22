<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


/**
 * Class Config
 *
 * @author Artem Brunevski
 */
class Amasty_Brands_Model_Observer_System_Config
{
    public function onSaveAfter(Varien_Event_Observer $observer)
    {
        if ($observer->getSection() == 'ambrands'){
            /** @var Amasty_Brands_Model_Mapper $mapper */
            $mapper = Mage::getSingleton('ambrands/mapper');
            $mapper->run();
            $mapper->assignProductsFromAttribute();
        }
    }
}