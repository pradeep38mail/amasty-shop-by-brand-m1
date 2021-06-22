<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

/**
 * Class Brand
 *
 * @author Artem Brunevski
 */
class Amasty_Brands_Model_Resource_Brand extends Mage_Catalog_Model_Resource_Abstract
{
    /** @var string */
    protected $_brandProductTable;

    /**
     * Main constructor
     */
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('ambrands_brand');
        $this->setConnection(
            $resource->getConnection('blog_read'),
            $resource->getConnection('blog_write')
        );

        $this->_brandProductTable = $this->getTable('ambrands/brand_product');
    }

    /**
     * Retrieve Default attribute model
     *
     * @return string
     */
    protected function _getDefaultAttributeModel()
    {
        return 'ambrands/resource_eav_attribute';
    }

    /**
     * Get positions of associated to brand products
     * @param Amasty_Brands_Model_Brand $brand
     * @return array
     */
    public function getProductsPosition(Amasty_Brands_Model_Brand $brand)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_brandProductTable, array('product_id', 'position'))
            ->where('brand_id = :brand_id');
        $bind = array('brand_id' => (int)$brand->getId());

        return $this->_getWriteAdapter()->fetchPairs($select, $bind);
    }

    /**
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Abstract
     */
    protected function _afterSave(Varien_Object $object)
    {
        $this->_saveBrandProducts($object);


        return parent::_afterSave($object);
    }

    /**
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Abstract|void
     */
    protected function _beforeSave(Varien_Object $object)
    {
        if ($object->isObjectNew()) {
            $positionAttributes = array(
                'topmenu_position',
                'slider_position',
                'leftmenu_position');
            foreach ($positionAttributes as $attr) {
                if(is_null($object->getData($attr))) {
                    $object->setStoreId(0)->setData($attr, 0);
                };
            }
        }
        return parent::_beforeSave($object);
    }

    /**
     * @param $brand
     * @return $this
     */
    protected function _saveBrandProducts($brand)
    {
        $id = $brand->getId();
        /**
         * new brand-product relationships
         */
        $products = $brand->getPostedProducts();

        /**
         * Example re-save brand
         */
        if ($products === null) {
            return $this;
        }

        /**
         * old brand-product relationships
         */
        
        //reload brand from DB in a case to avoid product entries duplication
        $oldProducts = Mage::getModel('ambrands/brand')->load($brand->getId())->getProductsPosition();
        $insert = array_diff_key($products, $oldProducts);
        $delete = array_diff_key($oldProducts, $products);

        /**
         * Find product ids which are presented in both arrays
         * and saved before (check $oldProducts array)
         */
        $update = array_intersect_key($products, $oldProducts);
        $update = array_diff_assoc($update, $oldProducts);

        $adapter = $this->_getWriteAdapter();

        /**
         * Delete products from brand
         */
        if (!empty($delete)) {
            $cond = array(
                'product_id IN(?)' => array_keys($delete),
                'brand_id=?' => $id
            );
            $adapter->delete($this->_brandProductTable, $cond);
        }

        /**
         * Add products to brand
         */
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $productId => $position) {
                $data[] = array(
                    'brand_id' => (int)$id,
                    'product_id'  => (int)$productId,
                    'position'    => (int)$position
                );
            }
            $adapter->insertMultiple($this->_brandProductTable, $data);
        }

        /**
         * Update product positions in brand
         */
        if (!empty($update)) {
            foreach ($update as $productId => $position) {
                $where = array(
                    'brand_id = ?'=> (int)$id,
                    'product_id = ?' => (int)$productId
                );
                $bind  = array('position' => (int)$position);
                $adapter->update($this->_brandProductTable, $bind, $where);
            }
        }

        return $this;
    }

    public function getRequestedAttributes($requested = null, $notInclude = false)
    {
        if (!$requested) {
            return $this->getSortedAttributes();
        }

        $allAttributes = $this->getAttributesByCode();
        $attributes = array();
        if ($notInclude) {
            $attributes = array_diff_key($allAttributes, array_flip($requested));
        } else {
            $attributes = array_intersect_key($allAttributes, array_flip($requested));
        }

        $setId = $this->getEntityType()->getDefaultAttributeSetId();
        // initialize set info
        Mage::getSingleton('eav/entity_attribute_set')
            ->addSetInfo($this->getEntityType(), $attributes, $setId);

        foreach ($attributes as $code => $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute_Abstract */
            if (!$attribute->isInSet($setId)) {
                unset($attributes[$code]);
            }
        }

        $this->_sortingSetId = $setId;
        uasort($attributes, array($this, 'attributesCompare'));
        return $attributes;
    }
    
}