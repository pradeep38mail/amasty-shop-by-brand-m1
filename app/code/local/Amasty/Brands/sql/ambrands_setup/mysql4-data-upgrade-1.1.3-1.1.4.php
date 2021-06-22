<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


$oldSettings = Mage::getModel('core/config_data')->getCollection();
$oldSettings->getSelect()->where('path = ?', 'ambrands/general/brands_page');
foreach ($oldSettings as $setting) {
    $identifier = $setting->getValue();
    $id = Mage::getModel('cms/page')->load($identifier, 'identifier')->getId();
    if ($id) {
        Mage::getConfig()
            ->saveConfig(
                'ambrands/general/brands_page',
                $id,
                $setting->getScope(),
                $setting->getScopeId()
            );
    }
}
