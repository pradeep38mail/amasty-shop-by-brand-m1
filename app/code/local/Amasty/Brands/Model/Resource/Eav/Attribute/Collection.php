<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


/**
 * Class Collection
 *
 * @author Artem Brunevski
 */
class Amasty_Brands_Model_Resource_Eav_Attribute_Collection extends
    Mage_Eav_Model_Resource_Entity_Attribute_Collection
{
    protected function _construct()
    {
        $this->_init('ambrands/resource_eav_attribute', 'eav/entity_attribute');
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        $entityTypeId = (int)Mage::getModel('eav/entity')->setType(Amasty_Brands_Model_Brand::ENTITY)
            ->getTypeId();
        $columns = $this->getConnection()->describeTable($this->getResource()->getMainTable());
        unset($columns['attribute_id']);
        $retColumns = array();
        foreach ($columns as $labelColumn => $columnData) {
            $retColumns[$labelColumn] = $labelColumn;
            if ($columnData['DATA_TYPE'] == Varien_Db_Ddl_Table::TYPE_TEXT) {
                $retColumns[$labelColumn] = Mage::getResourceHelper('core')->castField('main_table.'.$labelColumn);
            }
        }
        $this->getSelect()
            ->from(array('main_table' => $this->getResource()->getMainTable()), $retColumns)
            ->where('main_table.entity_type_id = ?', $entityTypeId);
        return $this;
    }
}