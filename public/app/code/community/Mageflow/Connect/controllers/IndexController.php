<?php

/**
 * IndexController.php
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
 * Mageflow_Connect_IndexController
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_IndexController extends Mage_Adminhtml_Controller_Action
{
    public $_publicActions = array('mflogin');

    /**
     * construct
     *
     */
    public function _construct()
    {
        parent::_construct();
    }

    /**
     * display migration
     *
     */
    public function migrateAction()
    {

        $this->loadLayout();
        $this->_setActiveMenu('mageflow/connect');
        $this->_addContent(
            $this->getLayout()->createBlock(
                'mageflow_connect/adminhtml_migrate',
                'connect.extensions'
            )
        );
        $this->renderLayout();
    }

    /**
     * index action
     */
    public function indexAction()
    {

    }

    /**
     * show action
     */
    public function showAction()
    {
        $this->loadLayout();
//        echo "Package details";
        $this->renderLayout();
    }
}
