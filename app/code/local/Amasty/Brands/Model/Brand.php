<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


/**
 * Class Entity
 *
 * @author Artem Brunevski
 */

/**
 * @method string getName()
 * @method Amasty_Brands_Model_Brand setName(string $value)
 * @method int getOptionId()
 * @method Amasty_Brands_Model_Brand setOptionId(int $value)
 * @method string getUrlKey()
 * @method Amasty_Brands_Model_Brand setUrlKey(string $value)
 * @method string getCreatedAt()
 * @method Amasty_Brands_Model_Brand setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Amasty_Brands_Model_Brand setUpdatedAt(string $value)
 * @method Amasty_Brands_Model_Brand setPostedProducts(array $value)
 * @method array getPostedProducts()
 */
class Amasty_Brands_Model_Brand extends Mage_Catalog_Model_Abstract
{
    const ENTITY = 'ambrands_brand';
    const FIELDSET_GENERAL = 'general';
    const FIELDSET_PAGE = 'page';
    const FIELDSET_TOPMENU = 'topmenu';
    const FIELDSET_LEFTMENU = 'leftmenu';
    const FIELDSET_SLIDER = 'slider';
    const CONFIG_NAVIGATION_DISPLAY = '1';
    const CONFIG_NAVIGATION_HIDE = '0';
    
    protected function _construct()
    {
        $this->_init('ambrands/brand');
    }

    /**
     * Validate attribute values
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function validate()
    {
        $validate = $this->_getResource()->validate($this);

        if (is_array($validate)) {
            foreach ($validate as $code => $error) {
                if ($error === true) {
                    Mage::throwException(Mage::helper('ambrands')->__(
                        'Attribute "%s" is required.',
                        $this->getResource()->getAttribute($code)->getFrontend()->getLabel()
                    ));
                }
                else {
                    Mage::throwException($error);
                }
            }
        }

        return $validate === true;
    }

    /**
     * Retrieve array of product id's
     *
     * array($productId => $position)
     *
     * @return array
     */
    public function getProductsPosition()
    {
        if (!$this->getId()) {
            return array();
        }

        $array = $this->getData('products_position');
        if (is_null($array)) {
            $array = $this->getResource()->getProductsPosition($this);
            $this->setData('products_position', $array);
        }
        return $array;
    }

    /**
     * @param $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        /** @var Mage_Catalog_Model_Category $category */
        $category = Mage::getSingleton('catalog/category');
        return $category->formatUrlKey($str);
    }

    /**
     * @param $fieldsetType
     * @return array
     */
    public function getFieldsetAttributeCodes($fieldsetType)
    {
        if($fieldsetType == self::FIELDSET_PAGE) {
            return array(
                'page_title',
                'description',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'cms_block_id',
                'bottom_cms_block_id');
        }
        $result = array();
        switch ($fieldsetType) {
            case self::FIELDSET_LEFTMENU:
                $result = array(
                    'icon_leftmenu',
                    'show_in_leftmenu',
                    'leftmenu_position');
                break;
            case self::FIELDSET_TOPMENU:
                $result = array(
                    'icon_topmenu',
                    'show_in_topmenu',
                    'topmenu_position');
                break;
            case self::FIELDSET_SLIDER:
                $result = array(
                    'image_slider',
                    'show_in_slider',
                    'slider_position');
                break;
            default:
                break;
        }
        return $result;
    }

    /**
     * Get collection instance
     *
     * @return Amasty_Brands_Model_Resource_Brand_Collection
     */
    public function getCollection()
    {
        $collection =  parent::getCollection();
        $attrCode = Mage::helper('ambrands')->getBrandAttributeCode();
        if (!$attrCode) {
            return $collection;
        }
        $optionIds = array();
        $attribute = Mage::getModel('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode);
        foreach ($attribute->getSource()->getAllOptions() as $option) {
            $optionIds[] = $option['value'];
        }
        $collection->addAttributeToFilter('option_id', array('in' => $optionIds));
        return $collection;
    }

    public function deleteOptions($deletedOptions)
    {
        $collection = parent::getCollection()->addFieldToFilter(
            'option_id',
            array('in' => $deletedOptions)
        );
        $collection->delete();
    }

    /**
     * Get Brand Page Title.
     * @return string
     */
    public function getTitle()
    {
        $pageTitle = trim($this->getData('page_title'));
        return $pageTitle ? $pageTitle : trim($this->getName());
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        $metaTitle = trim($this->getData('meta_title'));
        return $metaTitle ?: $this->getTitle();
    }

    /**
     * get Brand Page Url
     * @return string
     */
    public function getUrl()
    {
        if(!$this->getUrlKey()) {
            return '';
        }
        $url = Mage::getBaseUrl() . Mage::helper('ambrands')->getBrandsUrl() . $this->getUrlKey();

        $suffix = $this->_getUrlSuffix();
        if ($suffix == '') {
            return $url;
        }

        $l = strlen($suffix);
        if (strlen($url) < $l || substr_compare($url, $suffix, -$l) != 0) {
            $url.= $suffix;
        }

        return $url;
    }

    /**
     * @return string
     */
    protected function _getUrlSuffix()
    {
        $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
        if ($suffix && '/' != $suffix && '.' != $suffix[0]){
            $suffix = '.' . $suffix;
        }
        return $suffix;
    }

    /**
     * @return null|string
     */
    public function getImageUrl()
    {
        return $this->getImage()
            ? Mage::helper('ambrands')->getImageUrl() . $this->getImage()
            : null;
    }

    public function getDescription()
    {
        $cmsHelper = Mage::helper('cms');
        $processor = $cmsHelper->getBlockTemplateProcessor();
        return $processor->filter($this->getData('description'));
    }
}
