<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

/**
 * Class ImportExportInterface
 *
 * @author Artem Brunevski
 */

interface Amasty_Brands_Model_Abstract_ImportExportInterface
{
    /**
     * Permanent column names.
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */
    const COL_STORE    = '_store';
    const COL_BRAND_OPTION_NAME = '_brand_option_name';

    /**
     * col option
     */
    const COL_BRAND_OPTION = 'option_id';

    /**
     * URL key
     */
    const COL_BRAND_URL_KEY = 'url_key';
}