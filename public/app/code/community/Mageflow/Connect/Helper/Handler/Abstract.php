<?php
/**
 *
 * Mageflow_Connect_Helper_Handler_Abstract.php
 *
 * @author  sven
 * @created 03/31/2014 10:35
 */

class Mageflow_Connect_Helper_Handler_Abstract extends Mageflow_Connect_Helper_Data
{
    /**
     * sets data from array and saves object
     *
     * @param $itemModel
     * @param $filteredData
     *
     * @return array
     */
    public function saveItem($itemModel, $filteredData)
    {
        if (is_null($itemModel)) {
            return null;
        }

        try {
            $itemModel->setData($filteredData);
            $itemModel->save();
            Mage::helper('mageflow_connect/log')
                ->log(sprintf('Saved item with ID %s', $itemModel->getId()));
        } catch (Exception $e) {
            Mage::helper('mageflow_connect/log')->log(
                sprintf(
                    'Error occurred while saving item: %s',
                    $e->getMessage()
                )
            );
            Mage::helper('mageflow_connect/log')->log($e->getTraceAsString());
            return null;
        }
        return $itemModel;
    }

    /**
     * Creates changesetitem content from entity
     *
     * @param $content
     *
     * @return array
     */
    public function packContent($content)
    {
        return array();
    }
}