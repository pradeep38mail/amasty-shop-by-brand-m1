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
class Amasty_Brands_Model_Export_Entity_Brand
    extends Mage_ImportExport_Model_Export_Entity_Abstract
    implements Amasty_Brands_Model_Abstract_ImportExportInterface
{
    const LIMIT_BRANDS = 1000;

    /**
     * Array of pairs store ID to its code.
     *
     * @var array
     */
    protected $_storeIdToCode = array();

    /**
     * Attribute types
     * @var array
     */
    protected $_attributeTypes = array();

    /** @var Mage_Eav_Model_Entity_Attribute */
    protected $_brandAttribute;

    /** @var  array */
    protected $_brandAttributeOptions;

    public function __construct()
    {
        parent::__construct();

        $this
            ->_initAttributes()
            ->_initBrandAttribute()
            ->_initStores();
    }

    /**
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @param $storeId
     * @param $storeCode
     * @param array $dataRows
     * @param array $rowMultiselects
     */
    protected function _buildExportRow(
        Mage_Eav_Model_Entity_Collection_Abstract $collection,
        $storeId,
        $storeCode,
        array &$dataRows,
        array &$rowMultiselects
    ){
        $validAttrCodes  = $this->_getExportAttrCodes();

        $defaultStoreId  = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;

        foreach ($collection as $itemId => $item) { // go through all brands
            $rowIsEmpty = true; // row is empty by default

            foreach ($validAttrCodes as &$attrCode) { // go through all valid attribute codes
                $attrValue = $item->getData($attrCode);

                if ($attrCode === self::COL_BRAND_OPTION && $storeId === $defaultStoreId &&
                    array_key_exists($attrValue, $this->_brandAttributeOptions)
                ){
                    $dataRows[$itemId][$storeId][self::COL_BRAND_OPTION_NAME] =
                        $this->_brandAttributeOptions[$attrValue];
                }

                // do not save value same as default or not existent
                if ($storeId != $defaultStoreId
                    && isset($dataRows[$itemId][$defaultStoreId][$attrCode])
                    && $dataRows[$itemId][$defaultStoreId][$attrCode] == $attrValue
                ) {
                    $attrValue = null;
                }
                if (is_scalar($attrValue)) {
                    $dataRows[$itemId][$storeId][$attrCode] = $attrValue;
                    $rowIsEmpty = false; // mark row as not empty
                }

            }

            if ($rowIsEmpty) { // remove empty rows
                unset($dataRows[$itemId][$storeId]);
            } else {
                $dataRows[$itemId][$storeId][self::COL_STORE]    = $storeCode;
            }
            $item = null;
        }
    }

    /**
     * @param Mage_ImportExport_Model_Export_Adapter_Abstract $writer
     */
    protected function _buildWriteHeader(
        Mage_ImportExport_Model_Export_Adapter_Abstract $writer
    ){
        $validAttrCodes  = $this->_getExportAttrCodes();

        $headerCols = array_merge(
            array(
                self::COL_STORE,
                self::COL_BRAND_OPTION_NAME
            ),
            $validAttrCodes
        );

        $writer->setHeaderCols($headerCols);
    }

    /**
     * @param Mage_ImportExport_Model_Export_Adapter_Abstract $writer
     * @param array $dataRows
     * @param array $rowMultiselects
     */
    protected function _buildWriteRows(
        Mage_ImportExport_Model_Export_Adapter_Abstract $writer,
        array $dataRows,
        array $rowMultiselects
    ){
        $defaultStoreId  = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;

        foreach ($dataRows as $brandId => &$brandData) {
            foreach ($brandData as $storeId => &$dataRow) {
                if ($defaultStoreId == $storeId) {
                    $dataRow[self::COL_STORE] = null;
                }

                if(!empty($rowMultiselects[$brandId])) {
                    foreach ($rowMultiselects[$brandId] as $attrKey => $attrVal) {
                        if (!empty($rowMultiselects[$brandId][$attrKey])) {
                            $dataRow[$attrKey] = array_shift($rowMultiselects[$brandId][$attrKey]);
                        }
                    }
                }

                $writer->writeRow($dataRow);
            }
        }
    }

    /**
     * Export process and return contents of temporary file
     *
     * @deprecated after ver 1.9.2.4 use $this->exportFile() instead
     *
     * @return string
     */
    public function export()
    {
        $this->_prepareExport();

        return $this->getWriter()->getContents();
    }

    /**
     * Export process and return temporary file through array
     *
     * This method will return following array:
     *
     * array(
     *     'rows'  => count of written rows,
     *     'value' => path to created file
     * )
     *
     * @return array
     */
    public function exportFile()
    {
        $this->_prepareExport();

        $writer = $this->getWriter();

        return array(
            'rows'  => $writer->getRowsCount(),
            'value' => $writer->getDestination()
        );
    }

    /**
     * Prepare data for export and write its to temporary file through writer.
     *
     * @return void
     */
    protected function _prepareExport()
    {
        $offsetBrands = 0;
        $writer          = $this->getWriter();

        while (true)
        {
            ++$offsetBrands;

            $dataRows        = array();
            $rowMultiselects = array();

            // prepare multi-store values and system columns values
            foreach ($this->_storeIdToCode as $storeId => &$storeCode) { // go through all stores
                $collection = $this->_prepareEntityCollection(Mage::getModel('ambrands/brand')
                    ->getCollection())
                    ->setStoreId($storeId)
                    ->setPage($offsetBrands, self::LIMIT_BRANDS);

                if ($collection->getCurPage() < $offsetBrands) {
                    break;
                }
                $collection->load();

                if ($collection->count() == 0) {
                    break;
                }

                $this->_buildExportRow(
                    $collection,
                    $storeId,
                    $storeCode,
                    $dataRows,
                    $rowMultiselects
                );

                $collection->clear();
            }

            if ($collection->getCurPage() < $offsetBrands) {
                break;
            }

            // prepare catalog inventory information
            $brandsIds = array_keys($dataRows);

            // TODO: Implement products export
            $this->_prepareProducts($brandsIds);

            if ($offsetBrands == 1) {
                $this->_buildWriteHeader($writer);
            }

            $this->_buildWriteRows(
                $writer,
                $dataRows,
                $rowMultiselects
            );
        }
    }

    /**
     * Entity attributes collection getter.
     *
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function getAttributeCollection()
    {
        return Mage::getResourceModel('ambrands/eav_attribute_collection');
    }

    /**
     * EAV entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return Amasty_Brands_Model_Brand::ENTITY;
    }

    /**
     * @return $this
     */
    protected function _initAttributes()
    {
        /** @var /** @var Amasty_Brands_Model_Resource_Eav_Attribute $attribute */
        foreach ($this->getAttributeCollection() as $attribute) {
            $this->_attributeValues[$attribute->getAttributeCode()] = $this->getAttributeOptions($attribute);
            $this->_attributeTypes[$attribute->getAttributeCode()] =
                Mage_ImportExport_Model_Import::getAttributeType($attribute);
        }
        return $this;
    }

    protected function _initBrandAttribute()
    {
        /** @var Amasty_Brands_Model_Config $config */
        $config = Mage::getSingleton('ambrands/config');
        $this->_brandAttribute = $config->getBrandAttribute();
        foreach($config->getBrandAttributeOptions(false) as $option) {
            $this->_brandAttributeOptions[$option['value']] = $option['label'];
        }
        return $this;
    }

    /**
     * @param array $brandsIds
     * @return array
     * @throws Zend_Db_Statement_Exception
     */
    protected function _prepareProducts(array $brandsIds)
    {
        // TODO: Implement products export
        return array();
    }
}
