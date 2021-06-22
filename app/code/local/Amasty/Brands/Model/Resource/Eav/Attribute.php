<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


/**
 * Class Attribute
 *
 * @author Artem Brunevski
 */
class Amasty_Brands_Model_Resource_Eav_Attribute extends Mage_Eav_Model_Entity_Attribute
{
    const SCOPE_STORE                           = 0;
    const SCOPE_GLOBAL                          = 1;
    const SCOPE_WEBSITE                         = 2;

    protected $_globalAttributes = array(
        'option_id',
        'url_key'
    );

    /**
     * Return is attribute global
     *
     * @return integer
     */
    public function getIsGlobal()
    {
        return in_array($this->getAttributeCode(), $this->_globalAttributes);
    }

    /**
     * Retrieve attribute is global scope flag
     *
     * @return bool
     */
    public function isScopeGlobal()
    {
        return $this->getIsGlobal() == self::SCOPE_GLOBAL;
    }

    /**
     * Retrieve attribute is website scope website
     *
     * @return bool
     */
    public function isScopeWebsite()
    {
        return $this->getIsGlobal() == self::SCOPE_WEBSITE;
    }

    /**
     * Retrieve attribute is store scope flag
     *
     * @return bool
     */
    public function isScopeStore()
    {
        return !$this->isScopeGlobal() && !$this->isScopeWebsite();
    }

    /**
     * @return bool
     */
    public function getIsWysiwygEnabled()
    {
        return $this->getFrontendInput() == 'textarea';
    }

    /**
     * @return array
     */
    public function getApplyTo()
    {
        return array();
    }
}
