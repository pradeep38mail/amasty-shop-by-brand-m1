<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


/**
 * Class Collection.php
 *
 * @author Artem Brunevski
 */
class Amasty_Brands_Model_Resource_Brand_Collection
    extends Mage_Catalog_Model_Resource_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('ambrands/brand');
    }
}