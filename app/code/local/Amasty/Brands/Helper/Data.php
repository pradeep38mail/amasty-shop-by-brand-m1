<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */

/**
 * Class Data
 *
 * @author Artem Brunevski
 */
class Amasty_Brands_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * system configuration settings
     */
    const VAR_BRAND_ATTRIBUTE = 'ambrands/general/attribute';

    /** @var Mage_Eav_Model_Attribute */
    protected $_brandAttribute;

    /** @var array */
    protected $_brandOptionsCounts;

    /**
     * @return string
     */
    public function getBrandAttributeCode()
    {
        return Mage::getStoreConfig(self::VAR_BRAND_ATTRIBUTE);
    }

    /**
     * @return Mage_Eav_Model_Attribute
     */
    public function getBrandAttribute()
    {
        if ($this->_brandAttribute === null) {
            $attrCode = $this->getBrandAttributeCode();
            $entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
            /** @var Mage_Eav_Model_Attribute $attribute */
            $this->_brandAttribute = Mage::getModel('catalog/resource_eav_attribute')
                ->loadByCode($entityTypeId, $attrCode);
        }
        return $this->_brandAttribute;
    }

    /**
     * @return string
     */
    public function getBrandsPageUrl()
    {
        $identifier = Mage::getStoreConfig('ambrands/general/brands_page');
        $page = Mage::getModel('cms/page')
            ->load($identifier);
        if ($page && $page->getId()) {
            $identifier = $page->getIdentifier();
        }
        // get url by stored identifier or load identifier by CMS pageId.
        return Mage::getUrl($identifier);
    }

    public function getTopLinksPos()
    {
        return Mage::getStoreConfig('ambrands/general/top_links_pos');
    }
    /**
     * @var string $type
     * @return string
     */
    public function getImageFolderPath($type = 'image')
    {
        $folder = str_replace(array('image', '_'), '', $type);
        $path = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'amasty' . DS . 'brands' . DS;
        if ($folder) {
            $path .= $folder . DS;
        }
        return $path;
    }

    /**
     * @var string $type
     * @return string
     */
    public function getImageUrl($type = 'image')
    {
        $folder = str_replace(array('image', '_', 'icon'), '', $type);
        $path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'amasty' . DS . 'brands' . DS;
        if ($folder) {
            $path .= $folder . DS;
        }
        return $path;
    }

    /**
     * @param string $fileName
     * @param string $width
     * @param string $height
     * @param string $folderURL
     * @return string
     */
    public function getResizedImgUrl($fileName, $width = null, $height = null, $folderURL = null)
    {
        if (!$folderURL) {
            $folderURL = $this->getImageFolderPath();
        }

        $basePath = $folderURL . $fileName;
        $newPath = $this->getImageFolderPath() . 'resized' . DS . $width . 'x' . $height . DS . $fileName;
        if ($width != '') {
            if (file_exists($basePath) && is_file($basePath) && !file_exists($newPath)) {
                $height = $height ? intval($height) : null;
                $imageObj = new Varien_Image($basePath);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(FALSE);
                $imageObj->keepFrame(FALSE);
                $imageObj->resize($width, $height);

                $imageObj->save($newPath);
            }
            $resizedURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'amasty' . DS . 'brands' . DS
                . 'resized' . DS . $width . 'x' . $height . DS . $fileName;
        } else {
            $resizedURL =Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'amasty' . DS . 'brands' . DS . $fileName;
        }
        return $resizedURL;
    }

    /**
     * Apply Brand filter to the product collection.
     *
     * @param $productCollection
     * @param $brand
     * @return mixed
     */
    public function addBrandFilter($productCollection, $brand)
    {

        $joins = $productCollection->getSelect()->getPart(Zend_Db_Select::FROM);
        //like the category is anchor now
        foreach ($joins as &$node) {
            $node['joinCondition'] = str_replace(' AND cat_index.is_parent=1',' ', $node['joinCondition']);
        }

        $productCollection->getSelect()->setPart(Zend_Db_Select::FROM, $joins);


        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $attributeCode = $this->getBrandAttributeCode();
        $tableAlias = 'amproduct_index_eav';
        $attributeId = Mage::getModel('eav/config')->getAttribute('catalog_product', $attributeCode)->getAttributeId();

        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attributeId),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $productCollection->getStoreId()),
            $connection->quoteInto("{$tableAlias}.value = ?", $brand->getOptionId()),
        );

        $productCollection->getSelect()->join(
            array($tableAlias => Mage::getSingleton('core/resource')->getTableName('catalog/product_index_eav')),
            implode(' AND ', $conditions),
            array()
        );

        return $productCollection;
    }

    /**
     * Add positions to the product collection.
     *
     * @param $productCollection
     * @param $brand
     * @return mixed
     */
    public function addPositions($productCollection, $brand)
    {
        $columns  = $productCollection->getSelect()->getPart(Zend_Db_Select::COLUMNS);
        foreach ($columns as $key => $value) {
            if ($value[0] == 'cat_index' && $value[1] == 'position') {
                unset($columns[$key]);
                break;
            }
        }
        $productCollection->getSelect()->setPart(Zend_Db_Select::COLUMNS, $columns);

        $tableAlias = 'ambrands_product_position';
        $brandId = $brand->getId();
        $productCollection->getSelect()->joinLeft(
            array($tableAlias => Mage::getSingleton('core/resource')->getTableName('ambrands/brand_product')),
            "{$tableAlias}.product_id = e.entity_id"
            . " AND {$tableAlias}.brand_id = $brandId",
            array('cat_index_position' => 'position', 'cat_index.position' => 'position')
        );

        return $productCollection;
    }

    /**
     * rebuild Toolbar Url
     *
     * @param $url
     * @param $brandUrl
     * @return string
     */
    public function rebuildUrl($url, $brandUrl)
    {
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'];
        $pos = strpos($path, 'ambrands' . DS . 'index' . DS . 'view');
        if ($pos === false) {
            return $url;
        }
        $path = substr($path, 0, $pos) . $this->getBrandsUrl() . $brandUrl;
        $parsedUrl['path'] = $path;
        return $this->_restoreUrl($parsedUrl);
    }

    /**
     * Restre URL after parse_url()
     *
     * @param $parts
     * @return string
     */
    protected function _restoreUrl($parts)
    {
        return (isset($parts['scheme']) ? "{$parts['scheme']}:" : '') .
        ((isset($parts['user']) || isset($parts['host'])) ? '//' : '') .
        (isset($parts['user']) ? "{$parts['user']}" : '') .
        (isset($parts['pass']) ? ":{$parts['pass']}" : '') .
        (isset($parts['user']) ? '@' : '') .
        (isset($parts['host']) ? "{$parts['host']}" : '') .
        (isset($parts['port']) ? ":{$parts['port']}" : '') .
        (isset($parts['path']) ? "{$parts['path']}" : '') .
        (isset($parts['query']) ? "?{$parts['query']}" : '') .
        (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
    }

    /**
     * @return string
     */
    public function getBrandsUrl()
    {
        $res = trim(Mage::getStoreConfig('ambrands/general/url_key'));
        if (strlen($res)) {
            $res .= DS;
        }
        return $res;
    }

    /**
     * determine Amasty_Brands_Block_Catalog_Layer_View_Pure parent class
     *
     * @return bool
     */
    public function useSolr()
    {
        if (isset($this->_useSolr)) {
            return $this->_useSolr;
        }

        if ($this->isModuleEnabled('Enterprise_Search')) {
            /** @var Enterprise_Search_Helper_Data $helper */
            $helper = Mage::helper('enterprise_search');

            $routeName = Mage::app()->getRequest()->getRequestedRouteName();
            if ($routeName == 'catalog' || $routeName == 'amshopby') {
                $result = $helper->getIsEngineAvailableForNavigation(true);
            } else if ($routeName == 'catalogsearch') {
                $result = $helper->getIsEngineAvailableForNavigation(false);
            } else if ($routeName == 'adminhtml' || is_null($routeName)) {
                // process indexation
                $result = $helper->isActiveEngine();
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }

        $this->_useSolr = $result;
        return $result;
    }

    /**
     * interface for XML sitemap extension
     *
     * @param string $attrCode
     * @param string $optionId
     * @return string
     */
    public function getOptionUrl($attrCode, $optionId)
    {
        $brand = Mage::getModel('ambrands/brand')->loadByAttribute('option_id', $optionId);
        if (!$brand->getId()) {
            return '';
        }
        if (!$brand->getIsActive() || !$brand->getUrlKey()) {
            return '';
        }
        return $brand->getUrl();
    }

    /**
     * Returns HTML with brand image
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $mode (view, list, grid)
     * @return string
     */
    public function showIcon($product, $mode='view', $class = null)
    {
        $code = $this->getBrandAttributeCode();
        $optIds = trim(Mage::getResourceModel('catalog/product')
            ->getAttributeRawValue(
                $product->getId(),
                $code,
                Mage::app()->getStore()->getId()
            ), ',');
        if (!$optIds && $product->isConfigurable()){
            $usedProds = $product->getTypeInstance(true)->getUsedProducts(null, $product);
            foreach ($usedProds as $child){
                if ($child->getData($code)){
                    $optIds .= $child->getData($code) . ',';
                }
            }
        }
        if (!$optIds) {
            return '';
        }
        $brands = Mage::getModel('ambrands/brand')
            ->getCollection()
            ->addFieldToFilter('is_active', '1')
            ->addFieldToFilter('option_id', array('in'=> explode(',',$optIds)))
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('name');
        $block = Mage::getModel('core/layout')->createBlock('core/template')
            ->setArea('frontend')
            ->setTemplate('amasty/ambrands/links.phtml');
        $block->assign('_type', 'html')
            ->assign('_section', 'body')
            ->setBrands($brands)
            ->setClass($class)
            ->setMode($mode); // to be able to created different html

        return $block->toHtml();
    }

    /**
     * Shopby compatibility.
     *
     * @return bool
     */
    public function seoLinksActive()
    {
        if (!Mage::helper('core')->isModuleEnabled('Amasty_Shopby')) {
            return false;
        }
        return class_exists('Amasty_Shopby_Block_Catalog_Layer_View') && Mage::getStoreConfig('amshopby/seo/urls');
    }

    public function removeBrandUrlKey($origUrl)
    {
        $brandUrlKey = $this->getBrandsUrl();
        if(!$brandUrlKey)
            return ltrim($origUrl, '/');

        $len = strlen($brandUrlKey);

        $url = $origUrl;
        $url = ltrim($url, '/');

        if(substr($url,0,$len) == $brandUrlKey)
            $origUrl = substr($url, $len);

        return $origUrl;
    }

    /**
     * @param Mage_Catalog_Model_Resource_Product_Attribute_Collection $attributes
     */
    public function removeBrandFilter($attributes)
    {
        $brandId = Mage::app()->getRequest()->getParam('ambrand_id');
        if (!Mage::getStoreConfig('ambrands/general/attribute_filter') || $brandId) {
            $brandCode = Mage::helper('ambrands')->getBrandAttributeCode();
            if ($attributes->isLoaded()) {
                $attributes
                    ->clear()
                    ->setPageSize(false)
                    ->addFieldToFilter('attribute_code', array('neq' => $brandCode))
                    ->load();
            } else {
                $attributes->addFieldToFilter('attribute_code', array('neq' => $brandCode));
            }
        }
    }

    /**
     * @return array
     */
    public function getBrandOptionsCounts()
    {
        if ($this->_brandOptionsCounts === null) {
            $attribute = $this->getBrandAttribute();
            $this->_brandOptionsCounts = array();
            if ($attribute->getId()) {
                $filter = new Varien_Object();
                // important when used at category pages
                $layer = Mage::getModel('catalog/layer')
                    ->setCurrentCategory(Mage::app()->getStore()->getRootCategoryId());
                $filter->setLayer($layer);
                $filter->setStoreId(Mage::app()->getStore()->getId());
                $filter->setAttributeModel($attribute);
                $this->_brandOptionsCounts = Mage::getResourceModel('catalog/layer_filter_attribute')->getCount($filter);
            }
        }

        return $this->_brandOptionsCounts;
    }
}
