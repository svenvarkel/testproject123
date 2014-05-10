<?php
//needed for custom classcloading and namespaces
require_once 'Mageflow/Connect/Module.php';
/**
 * AbstractController.php
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
 * Mageflow_Connect_Controller_AbstractController
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Controller_AbstractController
    extends Mage_Adminhtml_Controller_Action
{

    /**
     * Class constuctor
     */
    public function _construct()
    {
        //include Mageflow client lib and its autoloader
        @include_once 'Mageflow/Connect/Module.php';
        $m = new \Mageflow\Connect\Module();
    }

    /**
     * @var Zend_Log
     */
    protected $_logger = null;

    /**
     * Returns logger helper instance
     *
     * @return Mageflow_Connect_Helper_Log
     */
    public function getLogger()
    {
        if (is_null($this->_logger)) {
            $this->_logger = Mage::helper('mageflow_connect/log');
        }
        return $this->_logger;
    }

    /**
     * Returns MageFlow API client instance with
     * authentication fields filled in
     *
     * @return \Mageflow\Connect\Model\Api\Mageflow\Client
     */
    public function getApiClient()
    {
        $configuration = new stdClass();
        $configuration->_consumerKey = Mage::app()->getStore()->getConfig(
            Mageflow_Connect_Model_System_Config::API_CONSUMER_KEY
        );
        $configuration->_consumerSecret = Mage::app()->getStore()->getConfig(
            Mageflow_Connect_Model_System_Config::API_CONSUMER_SECRET
        );
        $configuration->_token = Mage::app()->getStore()->getConfig(
            Mageflow_Connect_Model_System_Config::API_TOKEN
        );
        $configuration->_tokenSecret = Mage::app()->getStore()->getConfig(
            Mageflow_Connect_Model_System_Config::API_TOKEN_SECRET
        );
        $configuration->_company = \Mage::app()->getStore()->getConfig(
            \Mageflow_Connect_Model_System_Config::API_COMPANY
        );
        $configuration->_project = \Mage::app()->getStore()->getConfig(
            \Mageflow_Connect_Model_System_Config::API_PROJECT
        );
        $configuration->_instanceKey = \Mage::app()->getStore()->getConfig(
            \Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY
        );
        $client
            = new \Mageflow\Connect\Model\Api\Mageflow\Client($configuration);

        $this->getLogger()->log(
            $configuration,
            __METHOD__,
            __LINE__
        );

        return $client;
    }
}
