<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


/**
 * Class Amasty_Brands_Model_Mapper.php
 *
 * @author Artem Brunevski
 */
class Amasty_Brands_Model_Mapper extends Varien_Object
{
    /** @var Amasty_Brands_Model_Config  */
    protected $_config;

    /**
     * Amasty_Brands_Model_Mapper constructor.
     */
    public function __construct()
    {
        $this->_config = Mage::getSingleton('ambrands/config');
    }

    public function run()
    {
        /** @var Mage_Eav_Model_Entity_Attribute_Option $brandOption */
        foreach($this->_getUnmappedOptionsCollection() as $brandOption)
        {
            $this->_add($brandOption);
        }
    }

    /**
     * Add brand according to brand option
     * @param $brandOption
     * @param int $interaction
     * @throws Exception
     * @throws Zend_Db_Statement_Exception
     */
    protected function _add($brandOption, $interaction = 0)
    {
        /** @var Amasty_Brands_Model_Brand $brand */
        $brand = Mage::getModel('ambrands/brand');

        $optionValue = $this->isVesBrand() ? $brandOption->getTitle() : $brandOption->getValue();
        $urlKey = $optionValue;

        try {
            if ($interaction > 0) {
                $urlKey .= '-' . $interaction;
            }
            $brand->addData(array(
                'option_id' => $brandOption->getId(),
                'url_key' => $brand->formatUrlKey($urlKey),
                'name' => $optionValue,
                'is_active' => true,
            ));

            if ($brand->validate()){
                $this->_processProducts($brand, $brandOption->getId());
                $brand->save();
            }

        } catch (Zend_Db_Statement_Exception $e) {
            if ($e->getCode() === 23000 && $interaction < 10) { //duplicate entity
                $this->_add($brandOption, ++$interaction);
            } else {
                throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode());
            }
        }
    }

    /**
     * @param Amasty_Brands_Model_Brand $brand
     * @param int $brandOptionId
     * @return $this
     */
    protected function _processProducts($brand, $brandOptionId, $onlyAddMissedProducts = false)
    {
        $attrCode = $this->_getAttribute()->getAttributeCode();

        $attributeProductCollection = Mage::getModel('catalog/product')->getCollection();
        if ($attributeProductCollection->isEnabledFlat()) {
            $attributeProductCollection->addFieldToFilter(array(array('attribute' => $attrCode, 'eq' => $brandOptionId)));
        } else {
            $attributeProductCollection
                ->addAttributeToSelect('*')
                ->addAttributeToFilter($attrCode, $brandOptionId);
        }

        $attributeProducts = array_fill_keys($attributeProductCollection->getAllIds(), 0);

        $brandProducts = $brand->getProductsPosition();
        $extraProductIds = array_keys(array_diff_key($brandProducts, $attributeProducts));
        $missedProducts = array_diff_key($attributeProducts, $brandProducts);

        if (count($missedProducts)) {
            $brand->setPostedProducts($brandProducts + $missedProducts);
        }

        if (!$onlyAddMissedProducts && count($extraProductIds)) {
            $attributeProductCollection
                ->addFieldToFilter('entity_id', array('in' => $extraProductIds));;
            foreach ($attributeProductCollection as $product) {
                $product->setData($attrCode, $brandOptionId);
            }
            $attributeProductCollection->save();
        }

        return $this;
    }

    /**
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection
     */
    protected function _getUnmappedOptionsCollection()
    {
        /** @var Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection $collection */
        if ($this->isVesBrand()) {
            if (!Mage::helper('core')->isModuleEnabled('Ves_Brand')) {
                return array();
            }
            $collection = Mage::getModel("ves_brand/brand")->getCollection();
            $optionId = 'brand_id';
        } else {
            $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($this->_getAttribute()->getId())
                ->setStoreFilter($this->_getAttribute()->getStoreId());
            $optionId = 'option_id';
        }

        $collection->getSelect()->joinLeft(
            array('ambrands' => $collection->getTable('ambrands/entity')),
            'ambrands.option_id = main_table.' . $optionId,
            array()
        )->where('ambrands.entity_id is null');

        $collection->load();

        return $collection;
    }

    /**
     * @return Mage_Eav_Model_Entity_Attribute
     */
    protected function _getAttribute()
    {
        return $this->_config->getBrandAttribute();
    }

    /**
     * @return bool
     */
    protected function isVesBrand()
    {
        return $this->_getAttribute()->getAttributeCode() == 'vesbrand';
    }

    /**
     * Utility function. Execute it if something went wrong and brands havn't products assigned to them.
     * 
     * Script example:
     * <?php
     * require_once 'app/Mage.php';
     * Mage::app();
     * Mage::getModel('ambrands/mapper')->assignProductsFromAttribute();
     *
     */
    public function assignProductsFromAttribute()
    {
        $brandCollection = Mage::getModel('ambrands/brand')->getCollection();
        foreach ($brandCollection as $brand) {
            $this->_processProducts($brand, $brand->getOptionId(), true);
        }
        $brandCollection->save();
    }
}
