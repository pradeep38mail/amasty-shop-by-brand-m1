<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Base
 */


class Amasty_Base_Block_Extensions extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    const SEO_PARAMS = '?utm_source=extension&utm_medium=backend&utm_campaign=ext_list';

    protected $_template = 'amasty/ambase/modules.phtml';
    protected $moduleList;
    protected $dataMigrationList = array(
        'Amasty_Rma', 'Amasty_Label', 'Amasty_GiftCard', 'Magpleasure_Blog', 'Amasty_Customerattr'
    );
    protected $dataMigrationNames = array();

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);
        $html .= $this->_toHtml();
        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    public function getModuleList()
    {
        if (isset($this->moduleList)) {
            return $this->moduleList;
        }
        $array = array(
            'lastVersion' => array(),
            'hasUpdate' => array()
        );
        $modules = (array)Mage::getConfig()->getNode('modules')->children();
        ksort($modules);

        foreach ($modules as $moduleName => $moduleInfo) {
            $moduleFullName = explode('_', $moduleName);

            if (in_array($moduleName, $this->dataMigrationList)) {
                $tmpInfo = $this->getModuleInfo($moduleName);
                if (isset($tmpInfo['description'])) {
                    $this->dataMigrationNames[$moduleName] = $tmpInfo['description'];
                }
            }

            if (!in_array($moduleFullName[0], array('Amasty', 'Belitsoft', 'Mageplace', 'Magpleasure'))) {
                continue;
            }

            if (in_array($moduleName, array(
                'Amasty_Base', 'Magpleasure_Common', 'Magpleasure_Searchcore'
            ))) {
                continue;
            }

            if ((string)Mage::getConfig()->getModuleConfig($moduleName)->is_system == 'true') {
                continue;
            }

            if ($moduleInfo->active != 'true') {
                continue;
            }

            if (!is_array($module = $this->getModuleInfo($moduleName))) {
                continue;
            }

            if (!array_key_exists('hasUpdate', $module) || !$module['hasUpdate']) {
                $array['lastVersion'][] = $module;
            } else {
                $array['hasUpdate'][] = $module;
            }
        }

        $this->moduleList = $array;

        return $this->moduleList;
    }

    public function generateMigrationUrl($moduleName) {
        return "https://products.amasty.com/data-migration?utm_source="
            . strtolower(array_search($moduleName, $this->dataMigrationNames))
            . "&utm_medium=backend&utm_campaign=datamigration";
    }

    public function isMigrationModule($module)
    {
        return isset($module['description']) && in_array($module['description'], $this->dataMigrationNames);
    }

    protected function getModuleInfo($moduleCode)
    {
        $currentVer = Mage::getConfig()->getModuleConfig($moduleCode)->version;
        if (!$currentVer) {
            return '';
        }

        $url = '';
        // in case we have no data in the RSS
        $moduleName = (string)Mage::getConfig()->getNode('modules/' . $moduleCode . '/name');
        if ($moduleName) {
            $url = (string)Mage::getConfig()->getNode('modules/' . $moduleCode . '/url');
        } else {
            $moduleName = substr($moduleCode, strpos($moduleCode, '_') + 1);
        }
        $name = $moduleName;
        $baseKey = (string)Mage::getConfig()->getNode('modules/' . $moduleCode . '/baseKey');
        $allExtensions = Amasty_Base_Helper_Module::getAllExtensions();
        if ($allExtensions && isset($allExtensions[$moduleCode])) {
            if (is_array($allExtensions[$moduleCode])
                && !array_key_exists('name', $allExtensions[$moduleCode])
            ) {
                if (!empty($baseKey) && isset($allExtensions[$moduleCode][$baseKey])) {
                    $ext = $allExtensions[$moduleCode][$baseKey];
                } else {
                    $ext = end($allExtensions[$moduleCode]);
                }
            } else {
                $ext = $allExtensions[$moduleCode];
            }

            if ($ext['name']) {
                $name = $ext['name'];
            }
            $lastVer = $ext['version'];
            $url     = $ext['url'];

            $module = array(
                'description' => $name,
                'version' => end($currentVer),
                'lastVersion' => $lastVer,
                'hasUpdate' => version_compare($currentVer, $lastVer, '<')
            );

            if ($url) {
                $module['url'] = $url;
            }

            // in case if module output disabled
            if (Mage::getStoreConfig('advanced/modules_disable_output/' . $moduleCode)) {
                $module['disabled'] = true;
            }

            return $module;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getSeoparams()
    {
        return self::SEO_PARAMS;
    }
}
