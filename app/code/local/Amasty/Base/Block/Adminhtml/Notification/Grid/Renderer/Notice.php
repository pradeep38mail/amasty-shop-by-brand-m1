<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Base
 */


class Amasty_Base_Block_Adminhtml_Notification_Grid_Renderer_Notice
    extends Mage_Adminhtml_Block_Notification_Grid_Renderer_Notice
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $html = parent::render($row);
        $amastyLogo = $row->getData('is_amasty');
        if ($amastyLogo) {
            if ($row->getData('image_url')) {
                $html = '<div class="ambase-grid-message amasty-grid-logo" style="background: url('
                    . $row->getData('image_url') . ') no-repeat;">' . $html . '</div>';
            } else {
                $html = '<div class="ambase-grid-message amasty-grid-logo">' . $html . '</div>';
            }
        }

        return $html;
    }
}
