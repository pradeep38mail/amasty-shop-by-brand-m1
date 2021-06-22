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
class Amasty_Brands_Block_Adminhtml_Brand_Entity_Edit_Tab_General
    extends Mage_Adminhtml_Block_Catalog_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_tabs = array();

    protected function _construct()
    {
        parent::_construct();
        $hlp = Mage::helper('ambrands');
        $this->_tabs = array(
            Amasty_Brands_Model_Brand::FIELDSET_GENERAL => $hlp->__('General Information'),
            Amasty_Brands_Model_Brand::FIELDSET_TOPMENU => $hlp->__('Top Menu Bar'),
            Amasty_Brands_Model_Brand::FIELDSET_LEFTMENU => $hlp->__('Sidebar'),
            Amasty_Brands_Model_Brand::FIELDSET_SLIDER => $hlp->__('Brands Slider'),
        );
    }

    public function _prepareLayout()
    {
        parent::_prepareLayout();

        /** @var Amasty_Brands_Model_Brand $brand */
        $brand = Mage::registry(Amasty_Brands_RegistryConstants::CURRENT_BRAND);

        $form = new Varien_Data_Form();
        $form->setFieldNameSuffix('brand');
        $form->setDataObject($brand);

        $form->addField('in_brand_products', 'hidden',
            array(
                'name'      => 'brand_products'
            )
        );

        foreach ($this->_tabs as $code => $label) {
            $attributes = $this->_getAttributes($brand, $code);
            $fieldset = $form->addFieldset($code . '_fieldset', array(
                'legend' => Mage::helper('ambrands')->__($label)
            ));
            $this->_setFieldset($attributes, $fieldset);
        }

        Mage::dispatchEvent('ambrands_edit_prepare_form', array(
            'form'=>$form, 'url_key' => $brand->getUrlKey())
        );
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
        return $this->__('General');
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
     * @param string $fieldsetCode
     * @return array
     */
    protected function _getAttributes(Amasty_Brands_Model_Brand $brand, $fieldsetCode)
    {
        $brandResource =  $brand->getResource()
            ->loadAllAttributes($brand);
        if ($fieldsetCode == Amasty_Brands_Model_Brand::FIELDSET_GENERAL) {
            $attributes = array();
            foreach ($this->_tabs as $code => $value) {
                if ($code == Amasty_Brands_Model_Brand::FIELDSET_GENERAL) {
                    continue;
                }
                $attributes = array_merge($brand->getFieldsetAttributeCodes($code), $attributes);
            }
            $attributes = array_merge(
                $brand->getFieldsetAttributeCodes(Amasty_Brands_Model_Brand::FIELDSET_PAGE),
                $attributes
            );
            $attributes[] = 'option_id';    //dont show option_id
            return $brandResource->getRequestedAttributes($attributes, true);   //for get attributes which are not_in($attributes) array
        }
        $attributes = $brand->getFieldsetAttributeCodes($fieldsetCode);
        return $brandResource->getRequestedAttributes($attributes);
    }

    protected function _prepareForm()
    {
        parent::_prepareForm();
        $this->_setupDependencies();
        return $this;
    }

    protected function _setupDependencies()
    {
        /** @var Mage_Adminhtml_Block_Widget_Form_Element_Dependence $mapper */
        $mapper = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence');
        $mapper
            ->addFieldMap('slider_position', 'slider_position')
            ->addFieldMap('show_in_slider', 'show_in_slider')
            ->addFieldMap('topmenu_position', 'topmenu_position')
            ->addFieldMap('show_in_topmenu', 'show_in_topmenu')
            ->addFieldMap('leftmenu_position', 'leftmenu_position')
            ->addFieldMap('show_in_leftmenu', 'show_in_leftmenu')
            ->addFieldDependence('slider_position', 'show_in_slider', 1)
            ->addFieldDependence('topmenu_position', 'show_in_topmenu', 1)
            ->addFieldDependence('leftmenu_position', 'show_in_leftmenu', 1)
        ;

        $this->setChild('form_after', $mapper);
    }
}