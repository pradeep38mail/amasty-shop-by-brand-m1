<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

require_once Mage::getModuleDir('controllers', 'Amasty_Brands') . DS . 'Adminhtml' . DS . 'Ambrands' . DS . 'EntityController.php';
class Amasty_Brands_Adminhtml_Ambrands_LeftmenuController
    extends Amasty_Brands_Adminhtml_Ambrands_EntityController
{
    /**
     * @var string
     */
    protected $_posField = 'leftmenu_position';

    /**
     * grid entity ajax action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ambrands/adminhtml_brand_leftmenu_grid')->toHtml()
        );
    }

    protected function _addTitle()
    {
        $this->_title($this->__('Sidebar'));
        $this->_addBreadcrumb($this->__('Sidebar'), $this->__('Sidebar'));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/ambrands/leftmenu');
    }
}

