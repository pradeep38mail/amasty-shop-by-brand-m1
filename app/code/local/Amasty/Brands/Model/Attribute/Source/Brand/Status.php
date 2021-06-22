<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_Attribute_Source_Brand_Status extends Mage_Eav_Model_Entity_Attribute_Source_Boolean
{
    const VALUE_YES = 1;
    const VALUE_NO = 0;

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('adminhtml')->__('Enabled'),
                    'value' => self::VALUE_YES
                ),
                array(
                    'label' => Mage::helper('adminhtml')->__('Disabled'),
                    'value' => self::VALUE_NO
                ),
            );
        }
        return $this->_options;
    }

    public function toArray()
    {
        return array(
            self::VALUE_YES => Mage::helper('adminhtml')->__('Enabled'),
            self::VALUE_NO => Mage::helper('adminhtml')->__('Disabled')
        );
    }
}
