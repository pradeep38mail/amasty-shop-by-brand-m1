<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


/**
 * Class Grid.php
 *
 * @author Artem Brunevski
 */
abstract class Amasty_Brands_Block_Adminhtml_Brand_Base_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_sideType = '';
    protected $_label    = '';

    public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(true);
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getEntityClass()
    {
        return 'ambrands/brand';
    }

    /**
     * @return Mage_Core_Model_Store
     * @throws Exception
     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    /**
     * @return Amasty_Brands_Model_Resource_Brand_Collection
     * @throws Mage_Core_Exception
     */
    protected function _prepareCommonCollection()
    {
        $store = $this->_getStore();

        /** @var Amasty_Brands_Model_Resource_Brand_Collection $collection */
        $collection = Mage::getModel($this->_getEntityClass())->getCollection();

        $collection->setStoreId($store->getId());

        $collection
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('image');

        $select     = $collection->getSelect();
        $columnId   = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
        $dir        = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
        if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
            $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
            $select->order($columnId . ' ' . $dir);
        }

        return $collection;
    }

    /**
     * prepare columns for sidemenus
     * @throws Exception
     */
    protected function _prepareSidebarColumns()
    {
        $this->addColumn('entity_id', array(
            'header'=> Mage::helper('ambrands')->__('ID'),
            'type'  => 'number',
            'index' => 'entity_id',
        ));

        $this->addColumn('name', array(
            'header'=> Mage::helper('ambrands')->__('Name'),
            'type'  => 'text',
            'index' => 'name',
        ));

        $this->addColumn('url_key', array(
            'header'=> Mage::helper('ambrands')->__('URL Key'),
            'type'  => 'text',
            'index' => 'url_key',
        ));

        $this->addColumn('show_in_' . $this->_sideType, array(
            'header'    => Mage::helper('ambrands')->__('Show in %s', $this->_label),
            'index'     => 'show_in_' . $this->_sideType,
            'width'     => '80px',
            'type'      => 'options',
            'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
        ));

        $this->addColumn($this->_sideType . '_position', array(
            'header'    => Mage::helper('ambrands')->__('Position in %s', $this->_label),
            'align'     => 'left',
            'index'     => $this->_sideType . '_position',
            'type'      => 'number',
            'width'     => '80px',
            'sortable'  => true,
            'editable'  => true,
            'renderer'  => 'ambrands/adminhtml_brand_renderer_position'
        ));

        //add hidden field with currency filter, so empty custom filter can be stored in the session (because it can't be fully empty now).
        $this->addColumn('utility_field', array(
            'type'              => 'price',
            'currency_code'          => Mage::app()->getStore()->getBaseCurrency()->getCode(),
            'hidden'            => true,
            'column_css_class'  =>'no-display',
            'header_css_class'  =>'no-display',
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('catalog')->__('Action'),
            'type'      => 'action',
            'align'     =>'right',
            'filter'    => false,
            'sortable'  => false,
            'frame_callback' => array($this, 'showActions')
        ));

    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('brand_ids');
        $store =  (int) $this->getRequest()->getParam('store', 0);
        $attribute = 'show_in_' . $this->_sideType;
        
        $this->getMassactionBlock()->addItem('addToSidemenu', array(
            'label'    => Mage::helper('ambrands')->__('Add to the %s', $this->_label),
            'url'      => $this->getUrl('*/*/mass', array(
                'store' => $store,
                'status'  => '1',
                'attribute' => $attribute))
        ));
        $this->getMassactionBlock()->addItem('removeFromSidemenu', array(
            'label'    => Mage::helper('ambrands')->__('Remove from the %s', $this->_label),
            'url'      => $this->getUrl('*/*/mass', array(
                'store' => $store,
                'status'  => '0',
                'attribute' => $attribute))
        ));

        return $this;
    }

    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        $storeId  = (int) $this->getRequest()->getParam('store', 0);
        $params = array('id' => $row->getId());
        if ($storeId) {
            $params['store'] = $storeId;
        }
        return $this->getUrl('*/*/edit', $params);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function showActions($value, $row, $column)
    {
        $value = '1';
        $label = 'Add to the ';
        if (($row->getData('show_in_' . $this->_sideType)) ) {
            $value = '0';
            $label = 'Remove from the ';
        }
        $label .= $this->_label;

        $url = $this->getUrl('*/*/sidemenus',
            array(
                'brand_id'  => $row->getData('entity_id'),
                'store'     => $this->getRequest()->getParam('store'),
                'attribute' => 'show_in_' . $this->_sideType,
                'value'     => $value)
        );
        return '<a href="' . $url . '"><span>' . Mage::helper('ambrands')->__($label) . '</span></a>';
    }
}