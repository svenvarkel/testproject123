<?php
/**
 *
 * CmsBlock.php
 *
 * @author  sven
 * @created 02/26/2014 14:37
 */

class Mageflow_Connect_Helper_Handler_Cms_Media
    extends Mageflow_Connect_Helper_Handler_Abstract
{


    /**
     * Create changeset item from Mageflow_Connect_Model_Media_Index
     *
     * @param $content
     *
     * @return array|void
     */
    public function packContent($content)
    {
        Mage::helper('mageflow_connect/log')->log(print_r($content, true));
        $content['hex'] = bin2hex(file_get_contents($content['filename']));
        return $content;
    }

    /**
     * update or create CMS Media
     *
     * @param $filteredData
     *
     * @return array|null
     */
    public function handle($filteredData)
    {
        Mage::helper('mageflow_connect/log')->log(
            "Error occurred while tried to save media. Data follows:\n"
            . print_r($filteredData, true)
        );
        return $filteredData;
    }
}