<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


/**
 * Class EntityController
 *
 * @author Artem Brunevski
 */
class Amasty_Brands_Adminhtml_Ambrands_EntityController
    extends Mage_Adminhtml_Controller_Action
{
    protected $_posField = '';
    
    /**
     * grid action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('catalog/ambrands');
        $this->_title($this->__('Catalog'))
            ->_title($this->__('Shop By Brand'));
        $this->_addBreadcrumb($this->__('Catalog'), $this->__('Catalog'))
            ->_addBreadcrumb($this->__('Shop By Brand'), $this->__('REST Attributes'));
        $this->_addTitle();
        $this->renderLayout();
    }

    protected function _addTitle()
    {
        $this->_title($this->__('Manage Brands'));
        $this->_addBreadcrumb($this->__('Manage Brands'), $this->__('Manage Brands'));
    }

    /**
     * grid entity ajax action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ambrands/adminhtml_brand_entity_grid')->toHtml()
        );
    }

    /**
     * grid entity options ajax action
     */
    public function productsGridAction()
    {
        $this->_initBrand();
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ambrands/adminhtml_brand_entity_edit_tab_product')->toHtml()
        );
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Brand edit
     */
    public function editAction()
    {
        $storeId  = (int) $this->getRequest()->getParam('store');
        $brand = $this->_initBrand();

        $this->_title($brand->getId() ? $brand->getName() : $this->__('New Brand'));

        /**
         * Check if we have data in session (if duering category save was exceprion)
         */
        $data = Mage::getSingleton('adminhtml/session')->getAmastyBrandData(true);

        if (is_array($data) && array_key_exists('brand', $data)) {
            $brand->addData($data['brand']);
        }

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($storeId);
        }
        $this->_setActiveMenu('catalog/ambrands');
        $this->renderLayout();
    }

    /**
     * @param string $idFieldName
     * @return Amasty_Brands_Model_Brand
     */
    protected function _initBrand($idFieldName = 'id')
    {
        $this->_title($this->__('Brands'))->_title($this->__('Manage Brands'));

        $brandId = (int) $this->getRequest()->getParam($idFieldName);
        $storeId  = (int) $this->getRequest()->getParam('store', 0);
        $brand = Mage::getModel('ambrands/brand');
        $brand->setStoreId($storeId);

        if ($brandId) {
            $brand->load($brandId);
        }

        Mage::register(Amasty_Brands_RegistryConstants::CURRENT_BRAND, $brand);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($storeId);
        return $brand;
    }

    /**
     * Brand save
     */
    public function saveAction()
    {
        if (!$brand = $this->_initBrand()) {
            return;
        }

        $storeId        = $this->getRequest()->getParam('store', 0);
        $brandId        = $this->getRequest()->getParam('id');
        $redirectBack   = $this->getRequest()->getParam('back', false);

        if (($data = $this->getRequest()->getPost()) && array_key_exists('brand', $data)) {
            $brand->addData($data['brand']);

            /**
             * Check "Use Default Value" checkboxes values
             */
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $attributeCode) {
                    $brand->setData($attributeCode, false);
                }
            }

            try {
                if ($brand->isObjectNew()) {
                    if (!$brand->getOptionId()) {
                        $attrCode = Mage::helper('ambrands')->getBrandAttributeCode();
                        if ($attrCode) {
                            $newOptionId = $this->_createAttributeOption($brand->getName(), $brand, $attrCode);
                            if (!$newOptionId) {
                                throw new Mage_Core_Exception(Mage::helper('ambrands')->__('Can\'t create a corresponding attribute option.'));
                            }
                            $brand->setOptionId($newOptionId);
                        }
                    }
                }
                if ($brand->validate()) {
                    if (isset($data['brand']['brand_products'])) {
                        $products = Mage::helper('ambrands/string')->parseQueryStr($data['brand']['brand_products']);
                        $this->_processProductsAttribute(array_keys($products), $brand);
                        $brand->setPostedProducts($products);
                    }

                    $brand->save();
                    $brandId = $brand->getId();

                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('ambrands')->__('The brand has been saved.')
                    );
                }

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setAmastyBrandData($data);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage())
                    ->setAmastyBrandData($data);
                $redirectBack = true;
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $brandId,
                'store' => $storeId
            ));
        } else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }

    /**
     * WYSIWYG editor action for ajax request
     *
     */
    public function wysiwygAction()
    {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock('adminhtml/catalog_helper_form_wysiwyg_content', '', array(
            'editor_element_id' => $elementId,
            'store_id'          => $storeId,
            'store_media_url'   => $storeMediaUrl,
        ));

        $this->getResponse()->setBody($content->toHtml());
    }

    /**
     * Delete brand action
     */
    public function deleteAction()
    {
        if ($id = (int) $this->getRequest()->getParam('id')) {
            try {
                $brand = Mage::getModel('ambrands/brand')->load($id);

                $brand->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('ambrands')->__('The brand has been deleted.')
                );
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
                return;
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('ambrands')->__('An error occurred while trying to delete the brand.')
                );
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('_current'=>true, 'id'=>null)));
    }


    /**
     * save positions in sidebar/slider/topmenu
     */
    public function savepositionsAction()
    {
        $positions = $this->getRequest()->getParam($this->_posField);
        unset($positions['from'], $positions['to']);
        $positions = array_filter($positions, array($this, '_removeEmptyValues'));
        ksort($positions);

        $storeId = $this->getRequest()->getParam('store', 0);

        try {
            $brands = Mage::getModel('ambrands/brand')
                ->getCollection()
                ->setStoreId($storeId)
                ->addAttributeToSelect($this->_posField)
                ->addAttributeToFilter('entity_id', array('in' => array_keys($positions)))
                ->addAttributeToSort('entity_id', 'ASC');

            foreach ($brands as $brand) {
                $brand->setStoreId($storeId)
                    ->setData($this->_posField, abs(intval(array_shift($positions))))
                    ->save();
            }

            $brands->save();

        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('ambrands')->__('Brands positions has been updated.')
        );

        $this->_redirect('*/*/index', array(
            'store' => $storeId
        ));
        return;
    }

    protected function _removeEmptyValues($value)
    {
        return !empty($value) || $value === '0';
    }

    /**
     * show-in/remove-from sidebar/sliders/topmenu
     */
    public function sidemenusAction()
    {
        $brandId = (int) $this->getRequest()->getParam('brand_id');
        $storeId  = (int) $this->getRequest()->getParam('store', 0);
        $attribute = $this->getRequest()->getParam('attribute');
        $value = $this->getRequest()->getParam('value');
        $brand = Mage::getModel('ambrands/brand');
        $brand->setStoreId($storeId);
        
        if ($brandId) {
            $brand->load($brandId);
        } else {
            return;
        }
        try {
            $brand->setData($attribute, $value)->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('ambrands')->__('The brand has been updated.')
            );
        }
        catch (Mage_Core_Exception $e){
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
            return;
        }
        catch (Exception $e){
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('ambrands')->__('An error occurred while trying to update brand status.')
            );
        }
        $this->_redirect('*/*/index', array(
            'store' => $storeId
        ));
        return;
    }

    public function massAction()
    {
        $storeId  = (int) $this->getRequest()->getParam('store', 0);
        $attribute =  $this->getRequest()->getParam('attribute');
        $status   = $this->getRequest()->getParam('status');
        if (is_null($status) || is_null($attribute)) {
            $this->_redirect('*/*/index', array(
                'store' => $storeId
            ));
        }
        $ids = $this->getRequest()->getParam('brand_ids');
        $brands = Mage::getModel('ambrands/brand')
            ->getCollection()
            ->addFieldToFilter('entity_id', array('in' => $ids));
        try {
            $collectionSize = $brands->getSize();
            foreach ($brands as $brand) {
                $brand->setStoreId($storeId);
                $result = $this->_changeSingleBrand($brand, $attribute, $status);
            }
            $msg = Mage::helper('ambrands')->__($result, $collectionSize);
            Mage::getSingleton('adminhtml/session')->addSuccess($msg);
        }
        catch (Mage_Core_Exception $e){
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
            return;
        }
        catch (Exception $e){
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('ambrands')->__('An error occurred while trying to update brands status.')
            );
        }
        $this->_redirect('*/*/index', array(
            'store' => $storeId
        ));
        return;
    }

    /**
     * @param Amasty_Brands_Model_Brand $brand
     * @param string $attribute
     * @param string $status
     * @return $this
     */
    protected function _changeSingleBrand($brand, $attribute, $status)
    {
        if ($status === 'delete') {
            $brand->delete();
            $result = "Total of %d brand(s) have been deleted.";
        } else {
            $brand->setData($attribute, intval((bool)$status));
            $brand->save();
            $result = "Total of %d brand(s) have been updated.";
        }
        return $result;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/ambrands/list');
    }

    /**
     * @param string $label
     * @param Amasty_Brands_Model_Brand $brand
     * @param string $attrCode
     * @return option id
     */
    protected function _createAttributeOption($label, $brand, $attrCode)
    {
        if (!$label) {
            $label = 'new Brand';
        }
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attrCode);
        foreach ($attribute->getSource()->getAllOptions(true, true) as $option) {
            if ($label == $option['label']) {
                return $this->_createAttributeOption($label . '_1', $brand, $attrCode);
            }
        }

        $resourceSetup = $attribute->getResource();
        $tableOptions = $resourceSetup->getTable('eav/attribute_option');
        $tableOptionValues = $resourceSetup->getTable('eav/attribute_option_value');
        $attributeId = (int) $attribute->getId();

        // add option
        $data = array(
            'attribute_id' => $attributeId,
            'sort_order' => 0,
        );
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');

        $write->insert($tableOptions, $data);
        // add option label
        $optionId = (int) $read->lastInsertId($tableOptions, 'option_id');
        $data = array(
            'option_id' => $optionId,
            'store_id' => 0,
            'value' => $label,
        );
        $write->insert($tableOptionValues, $data);

        return $optionId;
    }

    /**
     * @param array $requestedProductIds
     * @param Amasty_Brands_Model_Brand $brand
     * @return $this
     */
    protected function _processProductsAttribute($requestedProductIds, $brand)
    {
        $attrCode = Mage::helper('ambrands')->getBrandAttributeCode();
        if (!$attrCode) {
            return $this;
        }
        $brandProductIds = array_keys($brand->getProductsPosition());

        $removedProductIds = array_diff($brandProductIds, $requestedProductIds);
        $removedCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', array('in' => $removedProductIds));
        foreach ($removedCollection as $product) {
            $product->setData($attrCode,'');
        }

        $newProductIds =  array_diff($requestedProductIds, $brandProductIds);
        $newCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', array('in' => $newProductIds));
        foreach ($newCollection as $product) {
            $product->setData($attrCode, $brand->getOptionId());
        }
        $removedCollection->save();
        $newCollection->save();
        return $this;
    }
}
