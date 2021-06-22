<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_System_Config_Source_Layout
{
    public function toOptionArray()
    {
        $layoutModel = Mage::getModel('page/source_layout');
        $options = array();
        foreach ($layoutModel->getOptions() as $value => $label) {
            if ($value == 'empty') {
                continue;
            }
            $options[] = array(
                'label' => $label,
                'value' => $value
            );
        }
        return $options;
    }
}