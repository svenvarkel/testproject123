<?php
/**
 *
 * Attributeset.php
 *
 * @author  sven
 * @created 02/26/2014 14:37
 */

class Mageflow_Connect_Helper_Handler_Catalog_Attributeset
    extends Mageflow_Connect_Helper_Handler_Abstract
{
    /**
     * create or update eav/entity_attribute_set from data array
     * all attributes used by attribute set must exist already
     * on update, pre-existing attribute groups shall
     * be deleted & new groups created
     *
     * @param $filteredData
     *
     * @return array|null
     */
    public function handle($filteredData)
    {
        $out = array();

        $itemFoundByIdentifier = false;
        $itemFoundByMfGuid = false;
        $foundItemsMatch = false;
        $itemModel = false;

        $itemModelByIdentifier = Mage::getModel('eav/entity_attribute_set')
            ->load($filteredData['attribute_set_name'], 'attribute_set_name');
        $itemModelByMfGuid = Mage::getModel('eav/entity_attribute_set')
            ->load($filteredData['mf_guid'], 'mf_guid');

        if ($itemModelByIdentifier->getAttributeSetId()) {
            $itemFoundByIdentifier = true;
        }
        if ($itemModelByMfGuid->getAttributeIdSet()) {
            $itemFoundByMfGuid = true;
        }
        if ($itemFoundByIdentifier && $itemFoundByMfGuid) {
            $idByIdent = $itemModelByIdentifier->getAttributeId();
            $idByGuid = $itemModelByMfGuid->getAttributeId();

            if ($idByGuid == $idByIdent) {
                $foundItemsMatch = true;
            }
        }

        if ($itemFoundByIdentifier && !$itemFoundByMfGuid) {
            Mage::helper('mageflow_connect/log')->log('case 01');
            $itemModel = $itemModelByIdentifier;
        }
        if (!$itemFoundByIdentifier && $itemFoundByMfGuid) {
            Mage::helper('mageflow_connect/log')->log('case 10');
            $itemModel = $itemModelByMfGuid;
        }
        if (!$itemFoundByIdentifier && !$itemFoundByMfGuid) {
            Mage::helper('mageflow_connect/log')->log('case 00');
            $itemModel = Mage::getModel('eav/entity_attribute_set');
        }
        if ($itemFoundByIdentifier && $itemFoundByMfGuid && $foundItemsMatch) {
            Mage::helper('mageflow_connect/log')->log('case 11-1');
            $itemModel = $itemModelByMfGuid;
        }
        if ($itemFoundByIdentifier && $itemFoundByMfGuid && !$foundItemsMatch) {
            Mage::helper('mageflow_connect/log')->log('case 11-0 error');
            //$itemModel = $itemModelByMfGuid;
        }

        $originalData = null;

        if ($itemModel->getData()) {
            $itemModel->save();
            $dummyChangeset = $this->createChangesetFromItem(
                'Mage_Eav_Model_Entity_Attribute_Set',
                $itemModel->getData()
            );
            $originalData = $dummyChangeset->getData();
        }

        if ($itemModel) {
            $allAttributesAreOk = true;

            // go over all Attributes in all Groups
            //and verify, if they are present
            // we can create Groups, but not Attributes

            $missingAttributes = array();

            foreach ($filteredData['groups'] as $group) {
                foreach ($group['attributes'] as $attribute) {

                    $attributeFoundByIdentifier = false;
                    $attributeFoundByMfGuid = false;
                    $foundAttributesMatch = false;

                    $attributeModelByMfGuid = Mage::getModel(
                        'eav/entity_attribute'
                    )->load($attribute['mf_guid'], 'mf_guid');
                    //FIXME Too much log will kill you
//                    Mage::helper('mageflow_connect/log')->log(
//                        $attributeModelByMfGuid
//                    );

                    $attributeCollection = Mage::getModel(
                        'eav/entity_attribute'
                    )
                        ->getCollection()
                        ->addFieldToFilter(
                            'attribute_code',
                            $attribute['attribute_code']
                        )
                        ->addFieldToFilter('entity_type_id', 4);
                    $attributeModelByIdentifier
                        = $attributeCollection->getFirstItem();

                    if ($attributeModelByIdentifier->getAttributeId()) {
                        $attributeFoundByIdentifier = true;
                    }
                    if ($attributeModelByMfGuid->getAttributeId()) {
                        $attributeFoundByMfGuid = true;

                    }
                    if ($attributeFoundByIdentifier
                        && $attributeFoundByMfGuid
                    ) {
                        $idByIdent
                            = $attributeModelByIdentifier->getAttributeId();
                        $idByGuid = $attributeModelByMfGuid->getAttributeId();

                        Mage::helper('mageflow_connect/log')->log(
                            'by mf_guid ' . $idByGuid
                        );
                        Mage::helper('mageflow_connect/log')->log(
                            'by ident ' . $idByIdent
                        );

                        if ($idByGuid == $idByIdent) {
                            $foundAttributesMatch = true;
                        }

                    }

                    if ((!$attributeFoundByIdentifier
                            && $attributeFoundByMfGuid)
                        || (!$attributeFoundByIdentifier
                            && !$attributeFoundByMfGuid)
                        || ($attributeFoundByIdentifier
                            && $attributeFoundByMfGuid
                            && !$foundAttributesMatch)
                    ) {
                        $missingAttributes[] = $attribute;
                        $allAttributesAreOk = false;
                        Mage::helper('mageflow_connect/log')->log(
                            'Attributes are NOT ok'
                        );
                    }
                }
            }
            if (sizeof($missingAttributes) > 0) {

                Mage::helper('mageflow_connect/log')->log(
                    'Missing attributes: ' . print_r($missingAttributes, true)
                );

                foreach ($missingAttributes as $attribute) {
                    $out['errors'][] = sprintf('Missing attribute: %s', $attribute['attribute_code']);
                }
            }
            // we have verified all attributes

            if ($allAttributesAreOk) {
                // attributes are ok

                // we need id for the attribute set

                if (!$itemModel->getAttributeSetId()) {
                    $attributeSetData = $filteredData;
                    $attributeSetData['groups'] = array();

                    $itemModel->setData($attributeSetData);
                    $itemModel->save();
                } else {
                    $attributeGroupCollection = Mage::getModel(
                        'eav/entity_attribute_group'
                    )
                        ->getCollection()
                        ->addFieldToFilter(
                            'attribute_set_id',
                            $itemModel->getAttributeSetId()
                        );
                    foreach ($attributeGroupCollection as $attributeGroup) {
                        $attributeGroup->delete();
                    }
                }


                $attributeSetData = array(
                    'groups' => array()
                );

                foreach ($filteredData['groups'] as $group) {
                    $attributeGroupCollection = Mage::getModel(
                        'eav/entity_attribute_group'
                    )
                        ->getCollection()
                        ->addFieldToFilter(
                            'attribute_group_name',
                            $group['attribute_group_name']
                        )
                        ->addFieldToFilter(
                            'attribute_set_id',
                            $itemModel->getAttributeSetId()
                        );
                    $attributeGroup = $attributeGroupCollection->getFirstItem();

                    if (!$attributeGroup->getAttributeGroupId()) {
                        $attributeGroup = Mage::getModel(
                            'eav/entity_attribute_group'
                        );
                    }

                    $groupData = $group;
                    $groupData['attribute_set_id']
                        = $itemModel->getAttributeSetId();
                    unset($groupData['attributes']);
                    $attributeGroup->setData($groupData);
                    $attributeGroup->save();
                    $groupData['attributes'] = array();

                    foreach ($group['attributes'] as $attribute) {
                        Mage::helper('mageflow_connect/log')->log(
                            'attribute code ' . $attribute['attribute_code']
                        );

                        $attributeModelByMfGuid = Mage::getModel(
                            'eav/entity_attribute'
                        )
                            ->load($attribute['mf_guid'], 'mf_guid');

                        $attributeCollection = Mage::getModel(
                            'eav/entity_attribute'
                        )
                            ->getCollection()
                            ->addFieldToFilter(
                                'attribute_code',
                                $attribute['attribute_code']
                            )
                            ->addFieldToFilter('entity_type_id', 4);
                        $attributeModelByIdentifier
                            = $attributeCollection->getFirstItem();

                        if ($attributeModelByMfGuid->getAttributeId()) {
                            Mage::helper('mageflow_connect/log')->log(
                                'attribute by mf_guid'
                            );
                            $groupData['attributes'][]
                                = $attributeModelByMfGuid;
                            $attributeModelByMfGuid->setAttributeSetId(
                                $itemModel->getAttributeSetId()
                            );
                            $attributeModelByMfGuid->setAttributeGroupId(
                                $attributeGroup->getAttributeGroupId()
                            );
                            $attributeModelByMfGuid->save();
                        } else {
                            Mage::helper('mageflow_connect/log')->log(
                                'attribute by identifier'
                            );
                            $groupData['attributes'][]
                                = $attributeModelByIdentifier;
                            $attributeModelByIdentifier->setAttributeSetId(
                                $itemModel->getAttributeSetId()
                            );
                            $attributeModelByIdentifier->setAttributeGroupId(
                                $attributeGroup->getAttributeGroupId()
                            );
                            $attributeModelByIdentifier->save();
                        }
                    }
                    $attributeGroup->setAttributes($groupData['attributes']);
                    $attributeGroup->save();
                    $attributeSetData['groups'][] = $attributeGroup;
                }

                $itemModel->setGroups($attributeSetData['groups']);
                $itemModel->save();
                Mage::helper('mageflow_connect/log')->log(__METHOD__);
                //FIXME Too much log will kill you ...
//                Mage::helper('mageflow_connect/log')->log($itemModel);

                $out = array(
                    'entity'        => $itemModel,
                    'original_data' => $originalData
                );
                return $out;
            }
        }
        Mage::helper('mageflow_connect/log')->log(
            "Error occurred while tried to save attribute set. Data follows:\n"
        //FIXME Too much log will kill you ...
//            . print_r($filteredData, true)
        );
        Mage::helper('mageflow_connect/log')->log(
            "Output: "
            . print_r($out, true)
        );
        return $out;
    }

    /**
     * @param $content
     *
     * @return array
     */
    public function packContent($content)
    {
        $originalContent = $content;
        //FIXME Too much log will kill you ...
//        Mage::helper('mageflow_connect/log')->log($content);
        Mage::helper('mageflow_connect/log')->log(__METHOD__);

        $attributeGroupCollection = Mage::getModel(
            'eav/entity_attribute_group'
        )
            ->getCollection()
            ->addFieldToFilter(
                'attribute_set_id',
                $content['attribute_set_id']
            );

        if (isset($content['groups'])) {
            unset($content['groups']);
        }
        $groups = array();
        foreach ($attributeGroupCollection as $group) {
            Mage::helper('mageflow_connect/log')->log(__METHOD__);
            //FIXME Too much log will kill you ...
//            Mage::helper('mageflow_connect/log')->log($group);
            $attributes = array();
            $actualAttributes = Mage::getModel('eav/entity_attribute')
                ->getCollection()
                ->setAttributeGroupFilter(
                    $group->getAttributeGroupId()
                );
            foreach ($actualAttributes as $actualAttribute) {
                $attributes[] = array(
                    'attribute_code' => $actualAttribute->getData()['attribute_code'],
                    'mf_guid'        => $actualAttribute->getData()['mf_guid']
                );
            }

            $attributeGroup = Mage::getModel('eav/entity_attribute_group')
                ->load(
                    $group->getData()['attribute_group_id'],
                    'attribute_group_id'
                );
            Mage::helper('mageflow_connect/log')->log(
                $attributeGroup->getData()
            );
            $data = $attributeGroup->getData();

            if (isset($data['attribute_group_id'])) {
                unset($data['attribute_group_id']);
            }
            if (isset($data['attribute_set_id'])) {
                unset($data['attribute_set_id']);
            }
            if (isset($data['attributes'])) {
                unset($data['attributes']);
            }

            $data['attributes'] = $attributes;
            $groups[] = $data;
        }
        if (isset($content['attribute_set_id'])) {
            unset($content['attribute_set_id']);
        }

        $content['groups'] = $groups;
        if (null !== $originalContent['remove_attributes']) {
            foreach ($originalContent['remove_attributes'] as $attribute) {
                $content['remove_attributes'][] = $attribute->getData();
            }
        }
        //FIXME Too much log will kill you ...
//        Mage::helper('mageflow_connect/log')->log($content);
        Mage::helper('mageflow_connect/log')->log(__METHOD__);
        return $content;
    }

}