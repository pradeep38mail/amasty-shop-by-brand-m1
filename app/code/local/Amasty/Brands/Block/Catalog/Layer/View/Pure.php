<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

if (Mage::helper('ambrands')->useSolr()) {
    $autoloader = Varien_Autoload::instance();
    $autoloader->autoload('Amasty_Brands_Block_Catalog_Layer_View_Enterprise');
} else {
    class Amasty_Brands_Block_Catalog_Layer_View_Pure extends Mage_Catalog_Block_Layer_View {}
}
