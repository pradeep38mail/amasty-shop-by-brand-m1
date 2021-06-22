<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


/**
 * Class Setup
 *
 * @author Artem Brunevski
 */
/*
DELETE FROM core_resource WHERE `code` = 'ambrands_setup';
DELETE FROM eav_entity_type WHERE `entity_type_code` = 'ambrands_brand';
DROP TABLE IF EXISTS amasty_brands_entity_varchar;
DROP TABLE IF EXISTS amasty_brands_entity_text;
DROP TABLE IF EXISTS amasty_brands_entity_int;
DROP TABLE IF EXISTS amasty_brands_entity_decimal;
DROP TABLE IF EXISTS amasty_brands_entity_datetime;
DROP TABLE IF EXISTS amasty_brands_entity_char;
DROP TABLE IF EXISTS amasty_brands_entity_product;
DROP TABLE IF EXISTS amasty_brands_entity;

*/
class Amasty_Brands_Model_Resource_Setup extends Mage_Eav_Model_Entity_Setup
{
    /**
     * @return array
     */
    public function getDefaultEntities()
    {
        $entities = array(
            Amasty_Brands_Model_Brand::ENTITY => array(
                'entity_model' => 'ambrands/brand',
                'attribute_model' => 'ambrands/resource_eav_attribute',
                'table' => 'ambrands/entity',
                'attributes' => array(
                    'option_id'                 => array(
                        'type'                       => 'static',
                        'label'                      => 'Brand Option',
                        'required'                   => true,
                        'sort_order'                 => 10,
                        'sort_order'                 => 10,
                    ),
                    'name'                      => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Name',
                        'input'                      => 'text',
                        'sort_order'                 => 20,
                        'required'                   => true
                    ),
                    'url_key'                   => array(
                        'type'                       => 'static',
                        'label'                      => 'Url Key',
                        'required'                   => true,
                        'sort_order'                 => 30
                    ),
                    'is_active'                 => array(
                        'type'                       => 'int',
                        'label'                      => 'Status',
                        'input'                      => 'select',
                        'source'                     => 'ambrands/attribute_source_brand_status',
                        'sort_order'                 => 40
                    ),
                    'image'                     => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Logo',
                        'input'                      => 'image',
                        'backend'                    => 'ambrands/attribute_backend_image',
                        'required'                   => false,
                        'sort_order'                 => 50
                    ),
                    'image_slider'              => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Slider Image',
                        'input'                      => 'image',
                        'backend'                    => 'ambrands/attribute_backend_image_slider',
                        'required'                   => false,
                        'sort_order'                 => 60
                    ),
                    'icon_topmenu'              => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Top Menu icon',
                        'input'                      => 'image',
                        'backend'                    => 'ambrands/attribute_backend_image_topmenu',
                        'required'                   => false,
                        'sort_order'                 => 70
                    ),
                    'icon_leftmenu'                 => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Left Menu Icon',
                        'input'                      => 'image',
                        'backend'                    => 'ambrands/attribute_backend_image_leftmenu',
                        'required'                   => false,
                        'sort_order'                 => 80
                    ),
                    'show_in_slider'            => array(
                        'type'                       => 'int',
                        'label'                      => 'Show in Slider',
                        'input'                      => 'select',
                        'source'                     => 'eav/entity_attribute_source_boolean',
                        'sort_order'                 => 90,
                        'required'                   => false,
                        'note'                       => 'Requires Slider Image or Logo'
                    ),
                    'slider_position'           => array(
                        'type'                       => 'int',
                        'label'                      => 'Slider Position',
                        'input'                      => 'text',
                        'sort_order'                 => 100,
                        'required'                   => false,
                        'note'                        => 'If Brands > Settings > Brands Slider > Sort By is set to "Position"',
                    ),
                    'show_in_topmenu'           => array(
                        'type'                       => 'int',
                        'label'                      => 'Show in Top Menu',
                        'input'                      => 'select',
                        'source'                     => 'eav/entity_attribute_source_boolean',
                        'sort_order'                 => 110,
                        'required'                   => false,
                    ),
                    'topmenu_position'          => array(
                        'type'                       => 'int',
                        'label'                      => 'Top Menu Position',
                        'input'                      => 'text',
                        'sort_order'                 => 120,
                        'required'                   => false,
                        'note'                        => 'If Brands > Settings > Top Menu > Sort By is set to "Position"',
                    ),
                    'show_in_leftmenu'          => array(
                        'type'                       => 'int',
                        'label'                      => 'Show in Left Menu',
                        'input'                      => 'select',
                        'source'                     => 'eav/entity_attribute_source_boolean',
                        'sort_order'                 => 130,
                        'required'                   => false,
                    ),
                    'leftmenu_position'         => array(
                        'type'                       => 'int',
                        'label'                      => 'Left Menu Position',
                        'input'                      => 'text',
                        'sort_order'                 => 140,
                        'required'                   => false,
                        'note'                       => 'If Brands > Settings > Left Menu > Sort By is set to "Position"',
                    ),
                    'page_title'                => array(
                        'type'                       => 'text',
                        'label'                      => 'Page Title',
                        'input'                      => 'text',
                        'sort_order'                 => 150,
                        'required'                   => false,
                        'note'                       => 'Title of Brand Page. Brand Name is Used if It is Empty.',
                    ),
                    'description'               => array(
                        'type'                       => 'text',
                        'label'                      => 'Description',
                        'input'                      => 'textarea',
                        'sort_order'                 => 160,
                        'required'                   => false,
                    ),
                    'meta_title'                => array(
                        'type'                       => 'text',
                        'label'                      => 'Meta Title',
                        'input'                      => 'text',
                        'sort_order'                 => 161,
                        'required'                   => false,
                        'note'                       => 'Meta Title of Brand Page. Page Title is Used if It is Empty.',
                    ),
                    'meta_keywords'             => array(
                        'type'                       => 'text',
                        'label'                      => 'Meta Keywords',
                        'input'                      => 'textarea',
                        'sort_order'                 => 170,
                        'required'                   => false,
                    ),
                    'meta_description'               => array(
                        'type'                       => 'text',
                        'label'                      => 'Meta Description',
                        'input'                      => 'textarea',
                        'sort_order'                 => 180,
                        'required'                   => false,
                    ),
                    'cms_block_id'              => array(
                        'type'                       => 'int',
                        'input'                      => 'select',
                        'label'                      => 'Top CMS block',
                        'required'                   => false,
                        'sort_order'                 => 190,
                        'input'                      => 'select',
                        'source'                     => 'ambrands/attribute_source_brand_cmsblock',
                    ),
                    'bottom_cms_block_id'            => array(
                        'type'                       => 'int',
                        'input'                      => 'select',
                        'label'                      => 'Bottom CMS block',
                        'required'                   => false,
                        'sort_order'                 => 200,
                        'input'                      => 'select',
                        'source'                     => 'ambrands/attribute_source_brand_cmsblock',
                    ),

                )
            )
        );
        return $entities;
    }
}
