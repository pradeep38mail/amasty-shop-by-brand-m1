<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Model_Observer
{
    public function pageBlockHtmlTopmenuGetHtmlBefore(Varien_Event_Observer $observer) {
        $topMenu = Mage::getModel('ambrands/topmenu', $observer->getMenu());
        $topMenu->addBrands();
    }

    public function coreBlockAbstractToHtmlAfter(Varien_Event_Observer $observer) {
        if ($observer->getBlock() instanceof Mage_Page_Block_Html_Topmenu){
            $html = $observer->getTransport()->getHtml();
            $html = str_replace(array(
                Amasty_Brands_Model_Topmenu::IMAGE_TAG, Amasty_Brands_Model_Topmenu::IMAGE_TAG_END),
                array( '<img ', ' >'), $html);
            $observer->getTransport()->setHtml($html);
        }
    }

    public function addUrlComment($observer)
    {
        $form = $observer->getEvent()->getForm();
        $urlElement = $form->getElement('url_key');
        if ($urlElement) {
            $store = Mage::app()->getRequest()->getParam('store', null);
            $brandUrl = Mage::getStoreConfig('ambrands/general/url_key', $store);
            $brandUrl .= $brandUrl ? '/' : '';
            $message = '<p class="note">Set URL Key for the Brand Page. Brand Page Url will Look Like www.example.com/'
                . $brandUrl . $observer->getEvent()->getUrlKey() . '.html</p>';
            $urlElement->setAfterElementHtml($message);
        }
        return $this;
    }

    public function catalogProductSaveAfter($observer)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();
        if ($product->hasDataChanges()) {
            try {
                $attrCode = Mage::helper('ambrands')->getBrandAttributeCode();
                $prevOptionId = $product->getOrigData($attrCode);
                $optionId = $product->getData($attrCode);
                if ($prevOptionId == $optionId) {
                    return $this;
                }
                if ($prevOptionId) {
                    $prevBrand = Mage::getModel('ambrands/brand')->loadByAttribute('option_id', $prevOptionId);
                    if ($prevBrand && $prevBrand->getId()) {
                        $prevProductIds = $prevBrand->getProductsPosition();
                        unset($prevProductIds[$product->getId()]);
                        $prevBrand->setPostedProducts($prevProductIds);
                        $prevBrand->save();
                    }
                }
                if ($optionId) {
                    $brand = Mage::getModel('ambrands/brand')->loadByAttribute('option_id', $optionId);
                    if ($brand && $brand->getId()) {
                        $productIds = $brand->getProductsPosition();
                        $productIds[$product->getId()] = 0;
                        $brand->setPostedProducts($productIds);
                        $brand->save();
                    }
                }
            } catch (Exception $e) {
                Mage::log($e->getTraceAsString(), null, 'ambrand_update_fault.log');
            }
        }
        return $this;
    }
}
