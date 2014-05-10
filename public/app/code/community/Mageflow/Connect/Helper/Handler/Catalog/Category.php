<?php
/**
 *
 * Category.php
 *
 * @author  sven
 * @created 02/26/2014 14:37
 */

class Mageflow_Connect_Helper_Handler_Catalog_Category
    extends Mageflow_Connect_Helper_Handler_Abstract
{
    /**
     * update or create catalog/category from data array
     *
     * @param $filteredData
     *
     * @return array|null
     */
    public function handle($filteredData)
    {
        $itemModel = null;

        $catalogCollection = Mage::getModel('catalog/category')
            ->getCollection()
            ->addFieldToFilter('mf_guid', $filteredData['mf_guid']);
        $itemModel = $catalogCollection->getFirstItem();

        $originalData = null;
        if (!is_null($itemModel)) {
            $itemModel = Mage::getModel('catalog/category');
        } else {
            $originalData = $itemModel->getData();
        }

        if ($itemModel->getData('entity_id')) {
            $filteredData['entity_id'] = $itemModel->getData('entity_id');
        }

        $originalPath = $filteredData['path'];
        unset($filteredData['path']);

        $rootCategory = Mage::getModel('catalog/category')
            ->getCollection()
            ->addFieldToFilter('parent_id', 0)
            ->load()
            ->getFirstItem();

        $parentCategory = Mage::getModel('catalog/category')
            ->getCollection()
            ->addFieldToFilter('mf_guid', $filteredData['parent_id'])
            ->load()
            ->getFirstItem();

        Mage::helper('mageflow_connect/log')->log(
            $rootCategory->getEntityId()
        );
        Mage::helper('mageflow_connect/log')->log(
            $parentCategory->getEntityId()
        );
        if ($parentCategory->getEntityId() == 0) {
            Mage::helper('mageflow_connect/log')->log('parent was not found');
            Mage::helper('mageflow_connect/log')->log(
                $filteredData['parent_id']
            );
            $parentId = $rootCategory->getEntityId();
            Mage::helper('mageflow_connect/log')->log('replacing parent');
            Mage::helper('mageflow_connect/log')->log(
                $filteredData['parent_id']
            );
        } else {
            $parentId = $parentCategory->getEntityId();
        }
        $mfGuid = $filteredData['mf_guid'];

        Mage::helper('mageflow_connect/log')->log($filteredData);
        $savedEntity = $this->saveItem($itemModel, $filteredData);
        $filteredData = $savedEntity->getData();
        Mage::helper('mageflow_connect/log')->log($filteredData);
        $savedEntity->setMfGuid($mfGuid);
        $savedEntity->move($parentId, $parentId);
        $filteredData = $savedEntity->getData();
        Mage::helper('mageflow_connect/log')->log($filteredData);
        $savedEntity->save();

        if ($savedEntity instanceof Mage_Catalog_Model_Category) {
            return array(
                'entity'        => $savedEntity,
                'original_data' => $originalData
            );
        }
        Mage::helper('mageflow_connect/log')->log(
            "Error occurred while tried to save Catalog Category.
            Data follows:\n"
            . print_r($filteredData, true)
        );
        return null;
    }

    /**
     * @param $content
     *
     * @return array
     */
    public function packContent($content)
    {
        if (isset($content['parent_id'])) {
            $parentCategory = Mage::getModel('catalog/category')
                ->load($content['parent_id']);
            $content['parent_id'] = $parentCategory->getMfGuid();
        }

        if (isset($content['path'])) {
            $pathIdList = explode('/', $content['path']);
            $fixedPath = array();
            foreach ($pathIdList as $pathId) {
                $categoryInPath = Mage::getModel('catalog/category')
                    ->load($pathId);
                $fixedPath[] = $categoryInPath->getMfGuid();
            }
            $content['path'] = implode('/', $fixedPath);
        }
        return $content;
    }

}