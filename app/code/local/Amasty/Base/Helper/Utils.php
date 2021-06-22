<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Base
 */

//@codingStandardsIgnoreFile
class Amasty_Base_Helper_Utils extends Mage_Core_Helper_Abstract
{
    public function _exit($code = 0)
    {
        exit($code);
    }

    public function _echo($a)
    {
        echo $a;
    }
}
