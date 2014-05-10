<?php

/**
 * Client
 *
 * PHP version 5
 *
 * @category Deployment
 * @package  Application
 * @author   Sven Varkel <sven@mageflow.com>
 * @license  http://mageflow.com/license/mageflow.txt
 *
 */

namespace Mageflow\Connect\Model\Api;

use Mageflow\Connect\Model\AbstractModel;

/**
 * Client
 *
 * @category Deployment
 * @package  Application
 * @author   Sven Varkel <sven@mageflow.com>
 * @license  http://mageflow.com/license/mageflow.txt
 *
 *
 * @method string getToken()
 * @method string getTokenSecret()
 * @method string getConsumerKey()
 * @method string getConsumerSecret()
 */
abstract class AbstractClient extends AbstractModel
{

    protected $_apiUrl = null;
    protected $_token;
    protected $_tokenSecret;
    protected $_consumerKey;
    protected $_consumerSecret;
    private $_logger;

    /**
     * Class constructor
     *
     * @param \stdClass $configuration
     *
     * @return \Mageflow\Connect\Model\Api\AbstractClient
     */
    public function __construct(\stdClass $configuration = null)
    {
        if (!is_null($configuration)) {
            foreach ($configuration as $key => $value) {
                $this->$key = $value;
            }
        }
        return $this;
    }


    /**
     *
     * @return \Mageflow\Connect\Helper\Logger
     */
    protected function getLogger()
    {
        if (is_null($this->_logger)) {
            $this->_logger = new \Mageflow\Connect\Helper\Logger();
        }
        return $this->_logger;
    }

}
