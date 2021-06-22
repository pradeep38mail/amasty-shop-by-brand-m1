<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

class Amasty_Brands_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard
{
    protected $request;
    
    public function match(Zend_Controller_Request_Http $request)
    {
        $this->request  = $request;
        $currentUrl = $this->request->getPathInfo();
        $currentUrl = Mage::helper('ambrands')->removeBrandUrlKey($currentUrl);
        if(trim($currentUrl,'/') === '')
            return false;

        $currentUrl = $this->_removeSuffix($currentUrl);

        $params = explode('/', $currentUrl);
        $brandUrl = array_shift($params);
        $params = implode('/', $params);
        /** @todo test mysql select & remove this if. change brands' is_active values to (1,2) [now (0,1)]*/
        if (defined('COMPILER_INCLUDE_PATH')) {
            $brandId = $this->getCurrentBrandId($brandUrl);
            if (!$brandId) {
                return false;
            }
        } else {
            $brand = Mage::getModel('ambrands/brand')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByAttribute('url_key', $brandUrl);
            if (!$brand || !$brand->getId()) {
                return false;
            }
            if (!$brand->getIsActive()) {
                return false;
            }
            $brandId = $brand->getId();
        }
        $this->request->setParam('ambrand_id', $brandId);

        if (!$this->proceedShopbySeo($params)) {
            return false;
        }

        $this->forwardAction();

        return true;
    }

    /**
     * @param string $params
     * @return bool
     */
    protected function proceedShopbySeo($params)
    {
        try {
            if (!(Mage::helper('ambrands')->seoLinksActive() && $params)) {
                return !$params;
            }
            $urlMode = Mage::getStoreConfig('amshopby/seo/urls');
            if ($urlMode == Amasty_Shopby_Model_Source_Url_Mode::MODE_SHORT) {
                $this->parseShopbyShort($params);
            } elseif ($urlMode == Amasty_Shopby_Model_Source_Url_Mode::MODE_MULTILEVEL) {
                $this->parseShopbyMultilevel($params);
            }

            return true;
        } catch (\Exception $e) {
            Mage::log($e->getMessage(), Zend_Log::ALERT, 'ambrands.log');
        }
    }

    /**
     * @param string $params
     */
    protected function parseShopbyShort($params)
    {
        $shortParser = Mage::getModel('amshopby/url_parser');
        $query = $shortParser->parseParams($params);
        $query = is_array($query) ? $query : array();
        
        $requestQuery = $this->request->getQuery();
        $requestQuery = is_array($requestQuery) ? $requestQuery : array();
        
        $query = array_merge($requestQuery, $query);
        Mage::register('amshopby_short_parsed', true);
        Mage::register('amshopby_required_seo_filters', array_keys($query));
       
        $this->request->setQuery($query);
    }

    /**
     * @param string $params
     */
    protected function parseShopbyMultilevel($params)
    {
        Mage::register('amshopby_current_params', explode('/', $params));
        Mage::helper('amshopby/url')->saveParams($this->request);
    }

    protected function getCurrentBrandId($brandUrl)
    {
        $isActiveId = intval(Mage::getResourceModel('eav/entity_attribute')
            ->getIdByCode(Amasty_Brands_Model_Brand::ENTITY, 'is_active'));
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $brandUrl = $connection->quote($brandUrl);
        $storeId = intval(Mage::app()->getStore()->getId());
        $table = Mage::getSingleton('core/resource')->getTableName('ambrands/entity');
        $storeAlias = "at_is_active";
        $defaultAlias = "at_is_active_default";
        $select = "
            SELECT `e`.`entity_id` FROM `$table` AS `e`
            INNER JOIN `{$table}_int` AS `$defaultAlias`
                ON (`$defaultAlias`.`entity_id` = `e`.`entity_id`) AND (`$defaultAlias`.`attribute_id` = '$isActiveId') AND `$defaultAlias`.`store_id` = 0
            LEFT JOIN `{$table}_int` AS `$storeAlias`
                ON (`$storeAlias`.`entity_id` = `e`.`entity_id`) AND (`$storeAlias`.`attribute_id` = '$isActiveId') AND (`$storeAlias`.`store_id` = $storeId) 
                WHERE (`e`.`url_key` = {$brandUrl}) AND (IF($storeAlias.value_id > 0, $storeAlias.value, $defaultAlias.value) = '1')
                LIMIT 1
        ";

        $id = $connection->fetchAll($select);
        $id = isset($id[0]) ? $id[0]['entity_id'] : null;

        return $id;
    }

    protected function forwardAction()
    {
        $realModule = 'Amasty_Brands';

        $this->request->setModuleName('ambrands');
        $this->request->setRouteName('ambrands');
        $this->request->setControllerName('index');
        $this->request->setActionName('view');
        $this->request->setControllerModule($realModule);

        $file = Mage::getModuleDir('controllers', $realModule) . DS . 'IndexController.php';
        include $file;

        //compatibility with 1.3
        $class = $realModule . '_IndexController';
        $controllerInstance = new $class($this->request, $this->getFront()->getResponse());

        $this->request->setDispatched(true);
        $controllerInstance->dispatch('view');
    }

    protected function _removeSuffix($url)
    {
        $suffix = $this->_getUrlSuffix();
        if ($suffix == '') {
            return $url;
        }

        $l = strlen($suffix);
        if (substr_compare($url, $suffix, -$l) == 0) {
            $url = substr($url, 0, -$l);
        }

        return $url;
    }

    protected function _getUrlSuffix()
    {
        $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
        if ($suffix && '/' != $suffix && '.' != $suffix[0]){
            $suffix = '.' . $suffix;
        }
        return $suffix;
    }

}
