<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

require_once Mage::getModuleDir('controllers', 'Amasty_Brands') . DS . 'Adminhtml' . DS . 'Ambrands' . DS . 'EntityController.php';
class Amasty_Brands_Adminhtml_Ambrands_TopmenuController
    extends Amasty_Brands_Adminhtml_Ambrands_EntityController
{
    /**
     * @var string
     */
    protected $_posField = 'topmenu_position';

    /**
     * grid entity ajax action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ambrands/adminhtml_brand_topmenu_grid')->toHtml()
        );
    }

    protected function _addTitle()
    {
        $this->_title($this->__('Top Menu Bar'));
        $this->_addBreadcrumb($this->__('Top Menu Bar'), $this->__('Top Menu Bar'));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/ambrands/topmenu');
    }
}