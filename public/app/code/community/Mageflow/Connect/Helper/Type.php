<?php

/**
 * Type
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @link       http://mageflow.com/
 */

/**
 * Type helper
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Type extends Mage_Core_Helper_Abstract
{

    /**
     * @param $type
     *
     * @return bool
     */
    public function isTypeEnabled($type)
    {
        $configNode = Mage::app()->getConfig()->getNode('default/mageflow_connect/supported_types/' . $type);
        if (null !== $configNode && $configNode[0] instanceof Mage_Core_Model_Config_Element) {
            /**
             * @var Mage_Core_Model_Config_Element $el
             */
            $el = $configNode[0];
            return $el->getAttribute('enabled') != 'false';
        }
        return true;
    }

    /**
     * @return array|mixed|string
     */
    public function getTypes()
    {
        $cacheId = md5(__METHOD__);
        $cache = Mage::app()->getCache();
        if ($cache->load($cacheId)) {
            $types = unserialize($cache->load($cacheId));
        } else {
            $typeNodeList = Mage::app()->getConfig()->getNode(
                'default/mageflow_connect/supported_types'
            );
            $types = array();
            /**
             * @var Mage_Core_Model_Config_Element $typeNode
             */
            foreach ($typeNodeList->children() as $typeNode) {
                if (
                    null == $typeNode->getAttribute('enabled')
                    || $typeNode->getAttribute('enabled') != 'false'
                ) {
                    $name = $typeNode->getName();
                    $data = $typeNode->asArray();
                    $types[$name] = (empty($data)) ? array() : $data;
                }
            }
            $cache->save(serialize($types), $cacheId);
        }
        return $types;
    }

    /**
     * This method returns list of types that
     * MageFlow supports.
     * NB! This list may change over MFx version changes.
     *
     * @return array
     */
    public function getSupportedTypes()
    {
        $nodeList = Mage::app()->getConfig()->getNode(
            'default/mageflow_connect/supported_types'
        )->asArray();
        $supportedTypes = array_keys($nodeList);
        return $supportedTypes;
    }
}
