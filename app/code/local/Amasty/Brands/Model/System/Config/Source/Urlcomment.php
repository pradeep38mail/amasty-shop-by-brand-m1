<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */
class Amasty_Brands_Model_System_Config_Source_Urlcomment
{
    public function getCommentText(){
        $store   = Mage::app()->getRequest()->getParam('store', null);
        $website = Mage::app()->getRequest()->getParam('website', null);
        if (is_null($store) && $website) {
            $urlKey = Mage::app()->getWebsite($website)->getConfig('ambrands/general/url_key');
        } else {
            $urlKey = Mage::getStoreConfig('ambrands/general/url_key', $store);
        }
        $urlKey = Mage::helper('core')->stripTags($urlKey, null, true);
        if (!$urlKey) {
            return "Brand page URL will Look Like<br>example.com/[url-key]/my-brand.html<br>";
        }
        return "Brand page URL will Look Like <br> example.com/<strong>$urlKey</strong>/my-brand.html <br> Leave empty to Get example.com/my-brand.html";
    }
}