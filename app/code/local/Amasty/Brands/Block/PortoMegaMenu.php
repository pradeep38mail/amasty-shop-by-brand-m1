<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Brands
 */


class Amasty_Brands_Block_PortoMegaMenu extends Smartwave_Megamenu_Block_Navigation
{
    public function drawCustomLinks()
    {
        $html = parent::drawCustomLinks();
        if (Mage::getStoreConfig('ambrands/topmenu/enabled')) {
            $topMenu = Mage::getModel('ambrands/topmenu');

            $brands = $topMenu->getItems();
            $result = array();
            foreach ($brands as $brand) {
                $model = Mage::getModel('catalog/category');
                $model->setName($brand->getName());
                $model->setUrl($brand->getUrl());
                $model->setIsActive(true);
                $result[] = $model;
            }

            $className = 'menu-item category-node-brands';
            if ($brands) {
                $className .= ' menu-item-has-children menu-parent-item';
            }

            $html .= '<li class="' . $className . '">
                <a href="' . Mage::helper('ambrands')->getBrandsPageUrl() . '" target="_self">'
                . Mage::helper('ambrands')->__('Brands') . '</a>';

            if ($brands) {
                $html .= '<div class="nav-sublist-dropdown" style="display: none;">';
                $html .= '<div class="container">';
                $html .= '<ul>';
                $html .= $this->drawColumns($result, 1, count($result), '', 'narrow');
                $html .= '</ul></div></div>';
            }

            $html .= '</li>';
        }

        return $html;
    }
}
