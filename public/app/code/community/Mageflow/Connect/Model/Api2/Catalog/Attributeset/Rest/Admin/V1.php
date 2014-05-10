<?php

/**
 * V1
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @link       http://mageflow.com/
 */

/**
 * V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Catalog_Attributeset_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{

    protected $_resourceType = 'catalog:attributeset';

    /**
     * Class constructor
     *
     * @return \Mageflow_Connect_Model_Api2_Catalog_Attributeset_Rest_Admin_V1
     */
    public function __construct()
    {
        return parent::__construct();
    }

    /**
     * retrieve
     *
     * @return array
     */
    public function _retrieve()
    {
        $out = array();
        foreach (
            Mage::getModel('eav/entity_type')
                ->getCollection()
                ->addFieldToFilter('entity_type_code', ['catalog_product'])
                ->load()
            as $allowedEntityType
        ) {

            $collection = $this->getWorkingModel()
                ->getCollection()
                ->setEntityTypeFilter(
                    $allowedEntityType->getData()['entity_type_id']
                );
            if (($key = trim($this->getRequest()->getParam('key'))) !== '') {
                $collection->addFieldToFilter('attribute_set_name', $key);
            }
            $items = $collection->load();

//            Mage::helper('mageflow_connect/log')->log($items->getItems());

            foreach ($items->getItems() as $item) {
                $c = $item->getData();
                $groups = array();
                foreach (
                    Mage::getModel('eav/entity_attribute_group')
                        ->getCollection()
                        ->addFieldToFilter(
                            'attribute_set_id',
                            $item->getData()['attribute_set_id']
                        )
                        ->load()
                        ->getItems()
                    as $group
                ) {
                    $g = new stdClass();
                    foreach ($group->getData() as $field => $entityField) {
                        $g->$field = $entityField;
                    }
                    $attributes = array();
                    foreach (
                        Mage::getModel('eav/entity_attribute')
                            ->getCollection()
                            ->setAttributeGroupFilter(
                                $group->getData()['attribute_set_id']
                            )
                            ->load()
                            ->getItems()
                        as $attribute
                    ) {
                        $a = new stdClass();
                        foreach (
                            $attribute->getData() as $field => $entityField
                        ) {
                            $a->$field = $entityField;
                        }
                        $attributes[] = $a;
                    }
                    $g->attributes = $attributes;
                    $groups[] = $g;
                }
                $c['groups'] = $groups;
                $out[] = $c;
            }
        }

        Mage::helper('mageflow_connect/log')->log($out);

        return $out;
    }

    /**
     * retrieve collection
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        return $this->_retrieve();
    }

    /**
     * Handles create (POST) request for cms/block
     *
     * @param array $filteredData
     *
     * @return array|string
     */
    public function _create(array $filteredData)
    {
        //FIXME Too much log will kill you ...
//        Mage::helper('mageflow_connect/log')->log(
//            sprintf('%s', print_r($filteredData, true))
//        );
        Mage::helper('mageflow_connect/log')->log(__METHOD__);
        //we shouldn't have any original data in case of creation

        $originalData = null;

        $handlerReturnArray = Mage::helper('mageflow_connect/handler_catalog_attributeset')
            ->handle($filteredData);

        if (sizeof($handlerReturnArray['errors']) > 0) {
            $this->_error("Could not save Attribute Set.", 500);
            foreach ($handlerReturnArray['errors'] as $errorMessage) {
                $this->_error($errorMessage, 500);
            }
            return array();
        }

        $entity = $handlerReturnArray['entity'];
        $originalData = $handlerReturnArray['original_data'];

        $rollbackFeedback = array();
        // send overwritten data to mageflow
        if (!is_null($originalData)) {
            $rollbackFeedback = $this->sendRollback(
                str_replace('_', ':', $this->_resourceType),
                $filteredData,
                $originalData
            );
        }
        $o = $entity->getData();
        $out = array();
        foreach ($o as $key => $value) {
            if (is_array($value)) {
                $c = new stdClass();
                foreach ($value as $k => $v) {
                    if ($v instanceof Mage_Core_Model_Abstract) {
                        if ($v instanceof Mage_Eav_Model_Entity_Attribute_Group) {
                            $attributes = array();
                            foreach ($v->attributes as $attribute) {
                                $attributeData = $attribute->getData();
                                if (empty($attributeData['store_labels'])) {
                                    $attributeData['store_labels'][''] = '';
                                }
                                $attributes[$attribute->attribute_code] = $attributeData;
                            }
                            $v->attributes = $attributes;
                            $c->$k = $v->getData();
                        } else {
                            $c->$k = $v->getData();
                        }
                    } else {
                        $c->$k = $v;
                    }
                }
                $out[$key] = $c;
            } else {
                $out[$key] = $value;
            }
        }
        $this->_successMessage("Successfully saved Attribute Set", 0, $out);
        //FIXME Too much log will kill you ...
        Mage::helper('mageflow_connect/log')->log(
            sprintf('%s', print_r($out, true))
        );
        Mage::helper('mageflow_connect/log')->log('Saved attribute set');
        return $out;
    }

    /**
     * multidelete
     *
     * @param array $filteredData
     */
    public function _multiDelete(array $filteredData)
    {
        Mage::helper('mageflow_connect/log')->log(sprintf('%s', $filteredData));

        $attributeSetEntity = Mage::getModel('eav/entity_attribute_set')
            ->load($filteredData['mf_guid'], 'mf_guid');

        $dummyChangeset = Mage::helper('mageflow_connect/data')
            ->createChangesetFromItem(
                'Mage_Eav_Model_Entity_Attribute_Set',
                $attributeSetEntity->getData()
            );
        $originalData = json_decode(
            $dummyChangeset->getData()['content'],
            true
        );
        $rollbackFeedback = array();
        // send overwritten data to mageflow
        if ($originalData) {
            $rollbackFeedback = $this->sendRollback(
                str_replace('_', ':', $this->_resourceType),
                $filteredData,
                $originalData
            );
        } else {
            $this->sendJsonResponse(
                ['notice' => 'target not found or empty, mf_guid='
                    . $filteredData['mf_guid']]
            );
        }
        try {
            $attributeSetEntity->delete();
            $this->sendJsonResponse(
                array_merge(
                    ['message' =>
                        'target deleted, mf_guid=' . $filteredData['mf_guid']],
                    $rollbackFeedback
                )
            );
        } catch (Exception $e) {
            $this->sendJsonResponse(
                array_merge(
                    ['delete error' => $e->getMessage()],
                    $rollbackFeedback
                )
            );
        }
    }

}
