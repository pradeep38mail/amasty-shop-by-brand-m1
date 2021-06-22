<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

if (Mage::helper('core')->isModuleEnabled('Amasty_Sorting')) {
    $autoloader = Varien_Autoload::instance();
    $autoloader->autoload('Amasty_Brands_Block_Catalog_Product_List_Toolbar_Sorting');
} else if (Mage::helper('core')->isModuleEnabled('Amasty_Shopby')) {
    $autoloader = Varien_Autoload::instance();
    $autoloader->autoload('Amasty_Brands_Block_Catalog_Product_List_Toolbar_Shopby');
}  else {
    class Amasty_Brands_Block_Catalog_Product_List_Toolbar_Pure extends Mage_Catalog_Block_Product_List_Toolbar {}
}
