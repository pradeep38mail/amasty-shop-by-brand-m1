<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Block_Adminhtml_Brand_Entity extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /** @var Amasty_Brands_Helper_Data  */
    protected $_helper;

    public function __construct()
    {
        $this->_blockGroup = 'ambrands';
        $this->_controller = 'adminhtml_brand_entity';
        $this->_headerText = Mage::helper('ambrands')->__('Manage Brands');
        $this->_addButtonLabel = Mage::helper('ambrands')->__('Add Brand');
        parent::__construct();
        $this->_helper = Mage::helper('ambrands');
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
            return false;
        }
        return true;
    }

    /**
     * @return mixed
     */
    public function getBrandAttributeCode()
    {
        return $this->_helper->getBrandAttributeCode();
    }
}