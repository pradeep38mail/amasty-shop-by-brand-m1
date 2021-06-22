<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

/** @var Amasty_Brands_Model_Resource_Setup $this */

$this->startSetup();
//drop foreign key because of Ves Brands. It uses its own option ids.
$this->getConnection()->dropForeignKey(
    $this->getTable('ambrands/entity'),
    'FK_AMASTY_BRANDS_ENTITY_OPTION_ID_EAV_ATTRIBUTE_OPTION_OPTION_ID'
);

$this->endSetup();
