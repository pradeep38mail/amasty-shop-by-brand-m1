<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Block_Adminhtml_Brand_Leftmenu_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action'  => $this->getUrl('*/*/save', array(
                    'id' => $this->getRequest()->getParam('id'),
                    'store' => $this->getRequest()->getParam('store')
                )),
                'method'  => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return Amasty_Brands_Model_Brand
     */
    public function getBrand()
    {
        /** @var Amasty_Brands_Model_Brand $brand */
        $brand = Mage::registry(Amasty_Brands_RegistryConstants::CURRENT_BRAND);
        return $brand;
    }
}