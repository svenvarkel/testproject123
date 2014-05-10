<?php
/**
 *
 * This class specifies types that are supported by MageFlow Extension
 *
 * Supported.php
 *
 * @author  sven
 * @created 12/20/2013 22:43
 */

class Mageflow_Connect_Model_Types_Supported extends Varien_Object
{
    /**
     * This method returns list of types that
     * MageFlow supports.
     * NB! This list may change over MFx version changes.
     *
     * @return array
     */
    public static function getSupportedTypes()
    {
        /**
         * @var Mageflow_Connect_Helper_Type $helper
         */
        $helper = Mage::helper('mageflow_connect/type');
        return $helper->getSupportedTypes();
    }
}