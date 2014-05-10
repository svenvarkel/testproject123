<?php

/**
 * LoginController.php
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
 * Mageflow_Connect_LoginController
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_LoginController extends Mageflow_Connect_Controller_AbstractController
{
    public $_publicActions = array('index', 'mfloginAction');

    /**
     * construct
     */
    public function _construct()
    {
        parent::_construct();
    }


    /**
     * index action
     */
    public function indexAction()
    {

    }

    /**
     * Make request to MF API to verify one-time token
     * and log in admin user if the token and e-mail are valid
     */
    public function mfloginAction()
    {
        $hash = $this->getRequest()->getParam('hash');
        $id = $this->getRequest()->getParam('id');
        Mage::helper('mageflow_connect/log')->log($hash);
        $client = $this->getApiClient();
        $result = json_decode($client->get('whois', array('id' => $id)),true);

        Mage::helper('mageflow_connect/log')->log(print_r($result, true));

        if ($hash !== $result['items']['auth_hash']) {
            $this->_redirect('adminhtml/dashboard/index');
            return;
        }

        $email = $result['items']['email'];

        $adminUserCollection = Mage::getModel('admin/user')->getCollection()
            ->addFieldToFilter('email', $email)
            ->addFieldToFilter('is_active', 1);
        $user = $adminUserCollection->getFirstItem();

        $session = Mage::getSingleton('admin/session');
        Mage::log(sprintf('%s(%s): %s', __METHOD__, __LINE__, print_r($session->getSessionId(), true)));
        Mage::dispatchEvent(
            'admin_user_authenticate_before',
            array(
                 'username' => $user->getUsername(),
                 'user'     => $user
            )
        );

        Mage::dispatchEvent(
            'admin_user_authenticate_after',
            array(
                 'username' => $user->getUsername(),
                 'password' => null,
                 'user'     => $user,
                 'result'   => true,
            )
        );
        $session->setUser($user);
//        $session->setIsFirstVisit(true);
        $session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
        Mage::dispatchEvent('admin_session_user_login_success', array('user' => $user));
        session_write_close();
        $this->_redirect('adminhtml/dashboard/index');
        return;
    }



}
