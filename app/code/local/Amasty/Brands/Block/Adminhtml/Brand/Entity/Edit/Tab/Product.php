<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


/**
 * Class Product
 *
 * @author Artem Brunevski
 */
class Amasty_Brands_Block_Adminhtml_Brand_Entity_Edit_Tab_Product
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ambrands_brand_entity_edit_tab_product');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Products');
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

    /**
     * @param $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in brand flag
        if ($column->getId() == 'in_brand') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            }
            elseif(!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     * @throws Exception
     */
    protected function _prepareCollection()
    {
        if ($this->getBrand()->getId()) {
            $this->setDefaultFilter(array('in_brand'=>1));
        }

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addStoreFilter($this->getRequest()->getParam('store'))
            ->joinField('position',
                'ambrands/brand_product',
                'position',
                'product_id=entity_id',
                'brand_id='.(int) $this->getRequest()->getParam('id', 0),
                'left');
        $this->setCollection($collection);

        return parent::_prepareCollection();
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

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        
        $this->addColumn('in_brand', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_brand',
            'values' => $this->_getSelectedProducts(),
            'align' => 'center',
            'index' => 'entity_id'
        ));

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('ambrands')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('ambrands')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('ambrands')->__('SKU'),
            'width'     => '80',
            'index'     => 'sku'
        ));
        $this->addColumn('price', array(
            'header'    => Mage::helper('ambrands')->__('Price'),
            'type'  => 'currency',
            'width'     => '1',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'price'
        ));
        $this->addColumn('position', array(
            'header'    => Mage::helper('ambrands')->__('Position'),
            'width'     => '1',
            'type'      => 'number',
            'index'     => 'position',
            'editable'  => true
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsGrid', array(
            'id' => $this->getBrand()->getId()
        ));
    }

    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');
        if (is_null($products)) {
            $products = $this->getBrand()->getProductsPosition();
            return array_keys($products);
        }
        return $products;
    }
}