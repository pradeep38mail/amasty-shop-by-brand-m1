<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Block_Adminhtml_Brand_Entity_Grid extends Amasty_Brands_Block_Adminhtml_Brand_Base_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('brand_entity_grid');
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     * @throws Mage_Core_Exception
     */
    protected function _prepareCollection()
    {
        $collection = $this->_prepareCommonCollection();
        $collection->addAttributeToSelect('is_active');
        $collection->addExpressionAttributeToSelect(
            'show_in_leftmenu',
            'IFNULL({{show_in_leftmenu}}, 0)',
            array('show_in_leftmenu' => 'show_in_leftmenu')
        );
        $collection->addExpressionAttributeToSelect(
            'show_in_topmenu',
            'IFNULL({{show_in_topmenu}}, 0)',
            array('show_in_topmenu' => 'show_in_topmenu')
        );
        $collection->addExpressionAttributeToSelect(
            'show_in_slider',
            'IFNULL({{show_in_slider}}, 0)',
            array('show_in_slider' => 'show_in_slider')
        );

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $hlp = Mage::helper('ambrands');
        $this->addColumn(
            'entity_id',
            array(
                'header' => $hlp->__('Id'),
                'type'   => 'number',
                'index'  => 'entity_id',
            )
        );

        $this->addColumn(
            'name',
            array(
                'header' => $hlp->__('Name'),
                'type'   => 'text',
                'index'  => 'name',
            )
        );

        $this->addColumn(
            'url_key',
            array(
                'header' => $hlp->__('URL Key'),
                'type'   => 'text',
                'index'  => 'url_key',
            )
        );

        $this->addColumn(
            'image',
            array(
                'header'   => $hlp->__('Logo'),
                'type'     => 'text',
                'index'    => 'image',
                'width'    => '97',
                'filter'   => false,
                'sortable' => false,
                'renderer' => 'ambrands/adminhtml_brand_renderer_image',
            )
        );

        $this->addColumn(
            'show_in_topmenu',
            array(
                'header'  => $hlp->__('Show in Top Menu'),
                'index'   => 'show_in_topmenu',
                'width'   => '80px',
                'type'    => 'options',
                'options' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
            )
        );

        $this->addColumn(
            'show_in_leftmenu',
            array(
                'header'  => $hlp->__('Show in Sidebar'),
                'index'   => 'show_in_leftmenu',
                'width'   => '80px',
                'type'    => 'options',
                'options' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
            )
        );

        $this->addColumn(
            'show_in_slider',
            array(
                'header'  => $hlp->__('Show in Slider'),
                'index'   => 'show_in_slider',
                'width'   => '80px',
                'type'    => 'options',
                'options' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
            )
        );

        $this->addColumn(
            'is_active',
            array(
                'header'  => $hlp->__('Status'),
                'index'   => 'is_active',
                'width'   => '80px',
                'type'    => 'options',
                'options' => Mage::getSingleton('ambrands/attribute_source_brand_status')->toArray(),
            )
        );


        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $params = array('id' => '$entity_id');
        if ($storeId) {
            $params['store'] = $storeId;
        }
        $this->addColumn(
            'action',
            array(
                'header'    => Mage::helper('adminhtml')->__('Action'),
                'width'     => '50px',
                'sortable'  => false,
                'filter'    => false,
                'type'      => 'action',
                'is_system' => true,
                'actions'   => array(
                    array(
                        'url'     => $this->getUrl('*/*/edit', $params),
                        'caption' => $hlp->__('Edit'),
                    )
                )
            )
        );

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('brand_ids');

        $values = array(
            Amasty_Brands_Model_Attribute_Source_Brand_Status::VALUE_YES => Mage::helper('adminhtml')->__('Enable'),
            Amasty_Brands_Model_Attribute_Source_Brand_Status::VALUE_NO  => Mage::helper('adminhtml')->__('Disable')
        );

        $this->getMassactionBlock()->addItem(
            'massChangestatus',
            array(
                'label'      => Mage::helper('ambrands')->__('Change Status'),
                'url'        => $this->getUrl(
                    '*/*/mass',
                    array(
                        'store'     => (int)$this->getRequest()->getParam('store', 0),
                        'attribute' => 'is_active'
                    )
                ),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'values' => $values,
                        'label'  => Mage::helper('adminhtml')->__('Status'),
                    )
                )
            )
        );

        $this->getMassactionBlock()->addItem(
            'massDelete',
            array(
                'label'   => Mage::helper('ambrands')->__('Delete Brands'),
                'url'     => $this->getUrl(
                    '*/*/mass',
                    array(
                        'store'  => (int)$this->getRequest()->getParam('store', 0),
                        'status' => 'delete'
                    )
                ),
                'confirm' => Mage::helper('adminhtml')->__('Are you sure?')
            )
        );

        return $this;
    }
}
