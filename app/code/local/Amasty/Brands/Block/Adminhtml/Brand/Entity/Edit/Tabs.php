<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


/**
 * Class Tabs
 *
 * @author Artem Brunevski
 */
class Amasty_Brands_Block_Adminhtml_Brand_Entity_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /** @var Mage_Core_Block_Abstract  */
    protected $_productsBlock;

    public function __construct()
    {
        parent::__construct();
        $this->setId('brandEntityTabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('ambrands')->__('Brand'));
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    public function getProductsBlock()
    {
        if ($this->_productsBlock === null){
            $this->_productsBlock = $this->getLayout()->createBlock(
                'ambrands/adminhtml_brand_entity_edit_tab_product',
                'ambrands_brand_entity_edit_tab_product'
            );
        }
        return $this->_productsBlock;
    }

    /**
     * @return Mage_Core_Block_Abstract
     * @throws Exception
     */
    protected function _prepareLayout()
    {
        $this->addTab('general', array(
            'label'     => Mage::helper('catalog')->__('General'),
            'content'   => $this->getLayout()->createBlock(
                'ambrands/adminhtml_brand_entity_edit_tab_general',
                'ambrands_brand_entity_edit_tab_general'
            )->toHtml()
        ));

        $this->addTab('page', array(
            'label'     => Mage::helper('ambrands')->__('Brand Page'),
            'content'   => $this->getLayout()->createBlock(
                'ambrands/adminhtml_brand_entity_edit_tab_page',
                'ambrands_brand_entity_edit_tab_general_page'
            )->toHtml()
        ));

        $brand = Mage::registry(Amasty_Brands_RegistryConstants::CURRENT_BRAND);
        if ($brand && $brand->getId()) {
            $this->addTab('products', array(
                'label' => Mage::helper('catalog')->__('Products'),
                'content' => $this->getProductsBlock()->toHtml(),
                'after' => 'ambrands_brand_entity_edit_tab_general'
            ));
        }

        return parent::_prepareLayout();
    }
}