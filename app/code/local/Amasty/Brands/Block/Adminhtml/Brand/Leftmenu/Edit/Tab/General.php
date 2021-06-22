<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


/**
 * Class General
 *
 * @author Artem Brunevski
 */
class Amasty_Brands_Block_Adminhtml_Brand_Leftmenu_Edit_Tab_General
    extends Amasty_Brands_Block_Adminhtml_Brand_Entity_Edit_Tab_General
{

    public function _prepareLayout()
    {
        Mage_Adminhtml_Block_Catalog_Form::_prepareLayout();

        /** @var Amasty_Brands_Model_Brand $brand */
        $brand = Mage::registry(Amasty_Brands_RegistryConstants::CURRENT_BRAND);

        $attributes = $this->_getAttributes($brand);

        $form = new Varien_Data_Form();
        $form->setFieldNameSuffix('brand');
        $form->setDataObject($brand);

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('ambrands')->__('Top Menu Information')
        ));

        $this->_setFieldset($attributes, $fieldset);

        $form->addValues($brand->getData());

        $this->setForm($form);

        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    /**
     * @param Amasty_Brands_Model_Brand $brand
     * @param string $fieldsetCode
     * @return array
     */
    protected function _getAttributes(Amasty_Brands_Model_Brand $brand, $fieldsetCode = Amasty_Brands_Model_Brand::FIELDSET_LEFTMENU)
    {
        $attrCodes = $brand->getFieldsetAttributeCodes($fieldsetCode);
        $attrCodes[] = 'name';
        return $brand->getResource()
            ->loadAllAttributes($brand)
            ->getRequestedAttributes($attrCodes);
    }
}