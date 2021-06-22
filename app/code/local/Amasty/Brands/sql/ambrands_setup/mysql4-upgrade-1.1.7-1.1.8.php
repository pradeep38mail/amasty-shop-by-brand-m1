<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

/** @var Amasty_Brands_Model_Resource_Setup $this */
$this->addAttribute(Amasty_Brands_Model_Brand::ENTITY, 'meta_title', array(
    'type'                       => 'text',
    'label'                      => 'Meta Title',
    'input'                      => 'text',
    'sort_order'                 => 161,
    'required'                   => false,
    'note'                       => 'Meta Title of Brand Page. Page Title is Used if It is Empty.',
));
