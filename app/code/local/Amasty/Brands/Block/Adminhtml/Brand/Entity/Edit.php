<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Block_Adminhtml_Brand_Entity_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId   = 'id';
        $this->_blockGroup = 'ambrands';
        $this->_controller = 'adminhtml_brand_entity';
        $this->_addButton('save_and_continue_edit', array(
            'class'   => 'save',
            'label'   => Mage::helper('catalogrule')->__('Save and Continue Edit'),
            'onclick' => 'editForm.submit($(\'edit_form\').action + \'back/edit/\')',
        ), 10);

        /** @var Amasty_Brands_Model_Brand $brand */
        $brand = Mage::registry(Amasty_Brands_RegistryConstants::CURRENT_BRAND);

        $this->_headerText    = Mage::helper('ambrands')->__('Edit Brand %s', $brand->getName());

        $this->removeButton('delete');
    }
}