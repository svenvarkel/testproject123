<?php

/**
 * Pullgrid.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Block_Adminhtml_Pullgrid
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Pullgrid
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{


    public function __construct()
    {
        $this->_controller = 'adminhtml_pullgrid';
        $this->_blockGroup = 'mageflow_connect';
        $this->_headerText = Mage::helper('mageflow_connect')->__(
            'Pull changes'
        );
        $this->removeButton('add');
        parent::__construct();
    }
}
