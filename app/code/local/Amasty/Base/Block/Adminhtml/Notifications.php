<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Base
 */


class Amasty_Base_Block_Adminhtml_Notifications extends Amasty_Base_Block_Extensions
{
    const AMUPDATES_COUNT = 'amupdates_count';
    const CACHE_LIFETIME = 86400;

    protected $_template = 'amasty/ambase/notifications.phtml';

    /**
     * @return false|int|mixed|null
     * @throws Zend_Cache_Exception
     */
    public function getUpdatesCount()
    {
        if (!$this->isSetNotification()) {
            return null;
        }

        $count = Mage::app()->getCache()->load(self::AMUPDATES_COUNT);
        if (!$count) {
            $modules = $this->getModuleList();
            $count = count($modules['hasUpdate']);
            Mage::app()->getCache()->save((string)$count, self::AMUPDATES_COUNT, array(), self::CACHE_LIFETIME);
        }

        return $count;
    }

    /**
     * @return bool
     */
    protected function isSetNotification()
    {
        $enabledNotifications = explode(',', Mage::getStoreConfig('ambase/feed/type'));

        return in_array(Amasty_Base_Model_Source_Type::AVAILABLE_UPDATE, $enabledNotifications);
    }
}
