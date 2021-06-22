<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Block_Adminhtml_Brand_Topmenu extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /** @var Amasty_Brands_Helper_Data  */
    protected $_helper;
    protected $_form = 'topmenu_form';

    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'ambrands';
        $this->_controller = 'adminhtml_brand_topmenu';
        $this->_headerText = Mage::helper('ambrands')->__('Top Menu Bar');
        $this->_removeButton('add');
        $this->_addButton('save', array(
            'label'   => Mage::helper('catalog')->__('Save Positions'),
            'class'   => 'save',
            'onclick'    => "document.getElementById('{$this->_form}').submit();",
        ));
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

    public function getGridHtml()
    {
        $action = $this->getUrl('*/*/savepositions', array(
            'store' => $this->getRequest()->getParam('store')
        ));
        $res = '<form id="' . $this->_form
            . '" action="' . $action
            . '" method="post" enctype="multipart/form-data">'
            . '<input name="form_key" type="hidden" value="'
            . Mage::getSingleton('core/session')->getFormKey() . '" />';
        $res .= parent::getGridHtml();
        $res .= '</form>';
        return $res;
    }

}