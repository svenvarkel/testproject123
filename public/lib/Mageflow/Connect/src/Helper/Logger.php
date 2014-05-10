<?php

/**
 * Logger
 *
 * PHP version 5
 *
 * @category Deployment
 * @package  Application
 * @author   Sven Varkel <sven@mageflow.com>
 * @license  http://mageflow.com/license/mageflow.txt
 *
 */

namespace Mageflow\Connect\Helper;

/**
 * Logger
 *
 * @category Deployment
 * @package  Application
 * @author   Sven Varkel <sven@mageflow.com>
 * @license  http://mageflow.com/license/mageflow.txt
 *
 */
class Logger
{

    /**
     * Logs debug level messages
     *
     * @param mixed   $message
     * @param string  $method
     * @param integer $line
     */
    public function debug($message, $method = null, $line = null)
    {

        if (function_exists('debug_backtrace')) {
            $backtrace = debug_backtrace();
            $method = $backtrace[1]['class'] . '::' . $backtrace[1]['function'];
            $line = $backtrace[0]['line'];
        }

        if (is_null($method)) {
            $method = __METHOD__;
        }
        if (is_null($line)) {
            $line = __LINE__;
        }


        $this->writelog($message, $method, $line, \Zend_Log::DEBUG);
    }

    /**
     *
     * @param type $message
     * @param type $method
     * @param type $line
     * @param type $level
     */
    private function writelog($message, $method, $line, $level)
    {


        $apiLog = \Mage::getBaseDir('var') . '/log/api.log';
        $systemLog = \Mage::getBaseDir('var') . '/log/system.log';

        $message = print_r($message, true);

        if (strlen($message) > 1024) {
            $message = substr($message, 0, 1024) . ' ...';
        }
        if (@touch($apiLog)) {
            $moduleWriter = new \Zend_Log_Writer_Stream($apiLog);
            $logger1 = new \Zend_Log($moduleWriter);
            $logger1->log(
                sprintf(
                    '%s(%s): %s',
                    $method,
                    $line,
                    $message
                ),
                $level
            );
        }

        if (@touch($systemLog)) {
            $globalWriter = new \Zend_Log_Writer_Stream($systemLog);
            $logger2 = new \Zend_Log($globalWriter);
            $logger2->log(
                sprintf(
                    '%s(%s): %s',
                    $method,
                    $line,
                    $message
                ),
                $level
            );
        }
    }

    /**
     * Log error level messages
     *
     * @param mixed $message
     */
    public function error($message)
    {
        if (function_exists('debug_backtrace')) {
            $backtrace = debug_backtrace();
            $method = $backtrace[1]['class'] . '::' . $backtrace[1]['function'];
            $line = $backtrace[0]['line'];
        }

        if (is_null($method)) {
            $method = __METHOD__;
        }
        if (is_null($line)) {
            $line = __LINE__;
        }
        $this->writelog($message, $method, $line, \Zend_Log::ERR);
    }

}