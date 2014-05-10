<?php

/**
 * Item
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Application
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 *
 */
//namespace Application;

/**
 * Item
 *
 * @category   MFX
 * @package    Application
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 *
 */
class Mageflow_Connect_Model_Resource_Changeset_Item
    extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Class constructor
     *
     * @return Item
     */
    public function _construct()
    {
        $this->_init('mageflow_connect/changeset_item', 'id');
    }

    /**
     * Truncates mageflow_changeset_item
     *
     * @return $this
     */
    public function truncate()
    {
        $this->_getWriteAdapter()->query('TRUNCATE TABLE ' . $this->getMainTable());
        return $this;
    }
}
