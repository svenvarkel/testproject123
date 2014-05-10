<?php
/**
 *
 * CmsBlock.php
 *
 * @author  sven
 * @created 02/26/2014 14:37
 */

class Mageflow_Connect_Helper_Handler_Cms_Abstract
    extends Mageflow_Connect_Helper_Handler_Abstract
{
    /**
     * @param $content
     *
     * @return array
     */
    public function packContent($content)
    {
        if (isset($content['stores']) && is_array($content['stores'])) {
            foreach ($content['stores'] as $key => $storeId) {
                if ($storeId != 0) {
                    $storeEntity = Mage::getModel('core/store')
                        ->load($storeId, 'store_id');
                    $content['stores'][$key] = $storeEntity->getCode();
                }
            }
        }
        return $content;
    }
}