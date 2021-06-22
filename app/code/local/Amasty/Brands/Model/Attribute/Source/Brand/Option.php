<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


/**
 * Class Option.php
 *
 * @author Artem Brunevski
 */
class Amasty_Brands_Model_Attribute_Source_Brand_Option
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
    implements Mage_Eav_Model_Entity_Attribute_Source_Interface
{
    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            /** @var Amasty_Brands_Model_Config $config */
            $config = Mage::getSingleton('ambrands/config');
            $this->_options = $config->getBrandAttributeOptions(true);
        }
        return $this->_options;
    }
}