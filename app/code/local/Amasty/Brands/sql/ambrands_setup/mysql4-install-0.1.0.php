<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


/**
 * @author Artem Brunevski
 */
/** @var Amasty_Brands_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 *  Create CMS Page
 */
$identifier = 'brands';
if (!Mage::getModel('cms/page')->load($identifier, 'identifier')->getId()) {
    $cmsPageData = array(
        'title' => 'All Brands Page',
        'identifier' => $identifier,
        'root_template' => 'one_column',
        'meta_keywords' => 'brands',
        'meta_description' => 'all brands page',
        'content' =>'
{{block type="ambrands/slider" template="amasty/ambrands/slider.phtml"}}
{{block type="ambrands/list" template="amasty/ambrands/list.phtml"}}',
        'stores' => array(0),//available for all store views
        'content_heading' => ''
    );
    Mage::getModel('core/config')->saveConfig('ambrands/general/brands_page', $identifier);
    Mage::getModel('cms/page')->setData($cmsPageData)->save();
}

/*
 * Create all entity tables
 */
$baseTableName = $installer->getTable('ambrands/entity');

$installer->createEntityTables('ambrands/entity');

$installer->getConnection()->dropColumn($baseTableName, 'attribute_set_id');
$installer->getConnection()->dropColumn($baseTableName, 'increment_id');
$installer->getConnection()->dropColumn($baseTableName, 'store_id');
$installer->getConnection()->dropColumn($baseTableName, 'is_active');

$installer->getConnection()->addColumn(
    $baseTableName,
    'option_id',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => true,
        'unsigned' => true,
        'comment' => 'Option Id',
        'after' => 'entity_id'
    )
);

$installer->getConnection()->addColumn(
    $baseTableName,
    'url_key',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => false,
        'comment' => 'URL Key',
        'after' => 'entity_type_id'
    )
);


$installer->getConnection()->addIndex(
    $installer->getTable(array('ambrands/entity', 'datetime')),
    $installer->getIdxName(
        $installer->getTable(array('ambrands/entity', 'datetime')),
        array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('ambrands/entity', 'decimal')),
    $installer->getIdxName(
        $installer->getTable(array('ambrands/entity', 'decimal')),
        array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('ambrands/entity', 'int')),
    $installer->getIdxName(
        $installer->getTable(array('ambrands/entity', 'int')),
        array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('ambrands/entity', 'text')),
    $installer->getIdxName(
        $installer->getTable(array('ambrands/entity', 'text')),
        array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('ambrands/entity', 'varchar')),
    $installer->getIdxName(
        $installer->getTable(array('ambrands/entity', 'varchar')),
        array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $baseTableName,
    $installer->getIdxName(
        array($baseTableName, 'int'),
        array('option_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('option_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $baseTableName,
    $installer->getIdxName(
        array($baseTableName, 'text'),
        array('url_key'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('url_key'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName($baseTableName, 'option_id', 'eav/attribute_option', 'option_id'),
    $baseTableName,
    'option_id',
    $installer->getTable('eav/attribute_option'),
    'option_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

/**
 * Create table 'catalog/category_product'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('ambrands/brand_product'))
    ->addColumn('brand_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ), 'Brand ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ), 'Product ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
    ), 'Position')
    ->addIndex($installer->getIdxName('catalog/category_product', array('product_id')),
        array('product_id'))
    ->addForeignKey($installer->getFkName('ambrands/brand_product', 'brand_id', 'ambrands/entity', 'entity_id'),
        'brand_id', $installer->getTable('ambrands/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('ambrands/brand_product', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Product To Brand Linkage Table');
$installer->getConnection()->createTable($table);

$installer->installEntities();

/**
 * Add Blocks to WhiteList
 */
$blockList = array(
    'ambrands/slider',
    'ambrands/list',
    'cms/block',
    'ambrands/search'
);
foreach ($blockList as $blockName) {
    try {
        /** @var Mage_Admin_Model_Block $block */
        $block = Mage::getModel('admin/block');
        if (is_object($block)) {
            //Not sure for the case, but some clients have errors
            $block->load($blockName, 'block_name');
            if (!$block->getId()) {
                $block->setData(array(
                    'block_name' => $blockName,
                    'is_allowed' => 1,
                ));
                $block->save();
            }
        }
    } catch (Exception $e) {
        // Magento version before 1.9.2.2: operation not required
    }
}



$installer->endSetup();
