<?php
/**
 * Observer.php
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
 * Mageflow_Connect_Model_Admin_Observer
 * This class extends Mage_Admin_Model_Observer
 *
 * @category MFX
 * @package  Application
 * @author   Sven Varkel <sven@mageflow.com>
 * @license  http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @link     http://mageflow.com/
 */
class Mageflow_Connect_Model_Admin_Observer extends Mage_Admin_Model_Observer
{

    /**
     * This method interferes to adminhtml controllers and
     * helps to login by Oauth
     *
     * @param Varien_Event_Observer $observer
     *
     * @return bool|void
     */
    public function actionPreDispatchAdmin($observer)
    {
        $openActions = array(
            'mflogin'
        );
        $session = Mage::getSingleton('admin/session');
        $request = Mage::app()->getRequest();
        $requestedActionName = $request->getActionName();
        if (in_array($requestedActionName, $openActions)) {
            $request->setDispatched(true);
            $session->refreshAcl();
            return;
        }
        parent::actionPreDispatchAdmin($observer);
    }

}
