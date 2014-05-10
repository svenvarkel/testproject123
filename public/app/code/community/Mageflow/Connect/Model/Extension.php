<?php

/**
 * Extension.php
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
 * Mageflow_Connect_Model_Extension
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Extension extends Mage_Core_Model_Abstract
{

    const CACHE_TAG = 'mageflow_connect_extension';

    /**
     * Class constructor
     *
     * @return \Mageflow_Connect_Model_Extension
     */
    public function __construct()
    {
        $this->_init('mageflow_connect/extension');
        return parent::_construct();
    }

}
