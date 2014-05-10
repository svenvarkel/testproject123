<?php

/**
 * Migrate.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Connect
 * @subpackage Block
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Block_Adminhtml_Migrate
 *
 * This class is used to display migration grid
 *
 * PLEASE READ THIS SOFTWARE LICENSE AGREEMENT ("LICENSE") CAREFULLY
 * BEFORE USING THE SOFTWARE. BY USING THE SOFTWARE, YOU ARE AGREEING
 * TO BE BOUND BY THE TERMS OF THIS LICENSE.
 * IF YOU DO NOT AGREE TO THE TERMS OF THIS LICENSE, DO NOT USE THE SOFTWARE.
 *
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @version    1.0
 * @author     MageFlow
 * @copyright  2014 MageFlow http://mageflow.com/
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 */
class Mageflow_Connect_Block_Adminhtml_Migrate
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->removeButton('add');
        $this->_controller = 'adminhtml_migrate';
        $this->_blockGroup = 'mageflow_connect';
        $this->_headerText = Mage::helper('mageflow_connect')->__(
            'Migrate changes'
        );

        /**
         * @var Mageflow_Connect_Helper_Type $typeHelper
         */
        $typeHelper = Mage::helper('mageflow_connect/type');

        if ($typeHelper->isTypeEnabled('media_file')) {
            $this->_addButton(
                'refresh',
                array(
                     'label'   => 'Refresh Media Index',
                     'onclick' => 'new Ajax.Request(\'' . $this->getUrl('*/*/refreshmediaindex') . '\')',
                     'class'   => '',
                )
            );
        }
    }
}
