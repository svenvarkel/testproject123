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

/**
 * Item
 *
 * @category   MFX
 * @package    Application
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 *
 */
class Mageflow_Connect_Model_Resource_Media_Index
    extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Class constructor
     *
     */
    public function _construct()
    {
        $this->_init('mageflow_connect/media_index', 'media_index_id');
    }
}
