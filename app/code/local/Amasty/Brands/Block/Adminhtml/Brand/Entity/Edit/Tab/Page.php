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
class Amasty_Brands_Block_Adminhtml_Brand_Entity_Edit_Tab_Page
    extends Mage_Adminhtml_Block_Catalog_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        /** @var Amasty_Brands_Model_Brand $brand */
        $brand = Mage::registry(Amasty_Brands_RegistryConstants::CURRENT_BRAND);
        $attrCodes = $brand->getFieldsetAttributeCodes(Amasty_Brands_Model_Brand::FIELDSET_PAGE);
        $attributes = $this->_getAttributes($brand, $attrCodes);

        $form = new Varien_Data_Form();
        $form->setFieldNameSuffix('brand');
        $form->setDataObject($brand);

        $fieldset = $form->addFieldset('page_fieldset', array(
            'legend' => Mage::helper('ambrands')->__('Page Settings')
        ));

        $this->_setFieldset($attributes, $fieldset);

        $form->addValues($brand->getData());

        $this->setForm($form);

        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }   
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Brand Page');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;

    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName(
                'ambrands/adminhtml_brand_entity_helper_image'
            ),
            'textarea' => Mage::getConfig()->getBlockClassName(
                'adminhtml/catalog_helper_form_wysiwyg'
            )
        );
    }

    /**
     * @param Amasty_Brands_Model_Brand $brand
     * @param array $attrCodes
     * @return array
     */
    protected function _getAttributes(Amasty_Brands_Model_Brand $brand, $attrCodes)
    {
        return $brand->getResource()
            ->loadAllAttributes($brand)
            ->getRequestedAttributes($attrCodes);
    }
}