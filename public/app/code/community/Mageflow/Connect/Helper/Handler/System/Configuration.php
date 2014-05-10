<?php
/**
 *
 * Configuration.php
 *
 * @author  sven
 * @created 02/26/2014 14:37
 */

class Mageflow_Connect_Helper_Handler_System_Configuration
    extends Mageflow_Connect_Helper_Handler_Abstract
{

    /**
     * create or update core/config_data from data array
     *
     * @param $filteredData
     *
     * @return array|null
     */
    public function handle($filteredData)
    {
        $itemModel = null;

        switch ($filteredData['scope']) {
            case 'default':
                $oldValue = Mage::app()->getStore()
                    ->getConfig($filteredData['path']);
                Mage::helper('mageflow_connect/log')->log($oldValue);
                $scopeId = 0;
                break;
            case 'websites':
                $website = Mage::getModel('core/website')
                    ->load($filteredData['website_code'], 'code');
                $oldValue = $website->getConfig($filteredData['path']);
                Mage::helper('mageflow_connect/log')->log($oldValue);
                $scopeId = $website->getWebsiteId();
                break;
            case 'stores':
                $store = Mage::getModel('core/store')
                    ->load($filteredData['store_code'], 'code');
                $oldValue = $store->getConfig($filteredData['path']);
                Mage::helper('mageflow_connect/log')->log($oldValue);
                $scopeId = $store->getStoreId();
                break;
        }

        $originalData = null;
        if (!is_null($oldValue)) {
            $originalData = $filteredData;
            $originalData['value'] = $oldValue;
        }

        Mage::helper('mageflow_connect/log')->log($scopeId);
        try {
            Mage::getModel('core/config')->saveConfig(
                $filteredData['path'],
                $filteredData['value'],
                $filteredData['scope'],
                $scopeId
            );
            Mage::helper('mageflow_connect/log')
                ->log('Config saved');
            return array(
                'entity'        => $filteredData,
                'original_data' => $originalData
            );
        } catch (Exception $e) {
            Mage::helper('mageflow_connect/log')->log(
                sprintf(
                    'Error occurred while saving item: %s',
                    $e->getMessage()
                )
            );
        }
        return null;
    }

    /**
     * @param $content
     *
     * @return array
     */
    public function packContent($content){
        Mage::helper('mageflow_connect/log')->log(print_r($content, true));
        $cleanedContent = array
        (
            'group_id'     => $content['group_id'],
            'store_code'   => $content['store_code'],
            'website_code' => $content['website_code'],
            'scope'        => $content['scope'],
            'scope_id'     => $content['scope_id'],
            'path'         => $content['path'],
            'value'        => $content['value'],
            'updated_at'   => $content['updated_at'],
            'created_at'   => $content['created_at'],
            'mf_guid'      => $content['mf_guid'],
        );
        $content = $cleanedContent;
        return $content;
    }

}