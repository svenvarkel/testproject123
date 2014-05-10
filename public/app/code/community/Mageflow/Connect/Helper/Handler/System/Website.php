<?php
/**
 *
 * Website.php
 *
 * @author  sven
 * @created 02/26/2014 14:37
 */

class Mageflow_Connect_Helper_Handler_System_Website
    extends Mageflow_Connect_Helper_Handler_Abstract
{


    /**
     * create or update website from changeset
     * all used categories must already exist with correct mf_guid's
     *
     * @param $filteredData
     *
     * @throws Exception
     * @return array|null
     */
    public function handle($filteredData)
    {
        $categoryIdList = array();
        foreach ($filteredData['groups'] as $group) {
            $categoryIdList[] = $group['root_category_id'];
        }
        $catalogCollection = Mage::getModel('catalog/category')
            ->getCollection()
            ->addFieldToFilter('mf_guid', $categoryIdList);
        $foundCategories = $catalogCollection->getSize();

        //FIXME it may not be necessary to have a category
//        if ($foundCategories != count($categoryIdList)) {
//            throw new Exception('Specified root category not found');
//            return null;
//        }

        $websiteEntity = Mage::getModel('core/website')
            ->load($filteredData['code'], 'code');

        $originalData = null;
        if (!is_null($websiteEntity)) {
            $originalData = $websiteEntity->getData();
        }

        $websiteEntity->setCode($filteredData['code']);
        $websiteEntity->setName($filteredData['name']);
        $websiteEntity->setSortOrder($filteredData['sort_order']);
        $websiteEntity->setIsDefault($filteredData['is_default']);
        $websiteEntity->save();

        Mage::helper('mageflow_connect/log')->log(
            sprintf(
                'Saved website with ID %s',
                print_r($websiteEntity->getId(), true)
            )
        );

        foreach ($filteredData['groups'] as $group) {
            $groupCollection = Mage::getModel('core/store_group')
                ->getCollection()
                ->addFieldToFilter('name', $group['name'])
                ->addFieldToFilter(
                    'website_id',
                    $websiteEntity->getWebsiteId()
                );

            $groupEntity = Mage::getModel('core/store_group')
                ->load($groupCollection->getFirstItem()->getGroupId());

            $groupEntity->setName($group['name']);

            $catalogCollection = Mage::getModel('catalog/category')
                ->getCollection()
                ->addFieldToFilter('mf_guid', $group['root_category_id']);
            $rootCategory = $catalogCollection->getFirstItem();
            $groupEntity->setRootCategoryId($rootCategory->getEntityId());
            $groupEntity->setWebsiteId($websiteEntity->getWebsiteId());
            $groupEntity->save();

            if ($groupEntity->getName() == $filteredData['default_group_id']) {
                $websiteEntity->setDefaultGroupId($groupEntity->getGroupId());
                $websiteEntity->save();
            }

            foreach ($group['stores'] as $store) {
                $storeEntity = Mage::getModel('core/store')
                    ->load($store['code'], 'code');

                $storeEntity->setCode($store['code']);
                $storeEntity->setName($store['name']);
                $storeEntity->setSortOrder($store['sort_order']);
                $storeEntity->setIsActive($store['is_active']);
                $storeEntity->setWebsiteId($websiteEntity->getWebsiteId());
                $storeEntity->setGroupId($groupEntity->getGroupId());
                $storeEntity->save();

                if ($storeEntity->getCode() == $group['default_store_id']) {
                    $groupEntity->setDefaultStoreId($storeEntity->getStoreId());
                }

            }
        }
        Mage::helper('mageflow_connect/log')->log(get_class($websiteEntity));

        if ($websiteEntity instanceof Mage_Core_Model_Website) {
            return array(
                'entity'        => $websiteEntity,
                'original_data' => $originalData
            );
        }
        Mage::helper('mageflow_connect/log')->log(
            "Error occurred while tried to save Website. Data follows:\n"
            . print_r($filteredData, true)
        );
        return null;

    }

    /**
     * @param $content
     */
    public function packContent($content)
    {
        $website = Mage::getModel('core/website')
            ->load($content['website_id']);

        $content = $website->getData();
        $groups = array();
        $groupCollection = Mage::getModel('core/store_group')
            ->getCollection()
            ->addFieldToFilter('website_id', $website->getWebsiteId());

        foreach ($groupCollection as $group) {
            $stores = array();
            $storeCollection = Mage::getModel('core/store')
                ->getCollection()
                ->addFieldToFilter('group_id', $group->getGroupId());

            foreach ($storeCollection as $store) {
                $storeData = $store->getData();
                unset($storeData['store_id']);
                unset($storeData['website_id']);
                unset($storeData['group_id']);

                $stores[] = $storeData;
            }

            $groupData = $group->getData();
            unset($groupData['website_id']);
            unset($groupData['group_id']);
            $groupData['stores'] = $stores;
            $rootCategory = Mage::getModel('catalog/category')
                ->load($groupData['root_category_id']);
            $defaultStore = Mage::getModel('core/store')
                ->load($groupData['default_store_id']);

            $groupData['root_category_id'] = $rootCategory->getMfGuid();
            $groupData['default_store_id'] = $defaultStore->getCode();
            $groups[] = $groupData;
        }

        $content = $website->getData();
        $content['groups'] = $groups;
        unset($content['website_id']);

        $defaultGroup = Mage::getModel('core/store_group')
            ->load($content['default_group_id']);
        $content['default_group_id'] = $defaultGroup->getName();
        return $content;
    }

}