<?php

/**
 * Renderer.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Application
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 */

/**
 * Mageflow_Connect_Block_Adminhtml_Pullgrid_Grid_Column_Renderer
 *
 * @category   MFX
 * @package    Application
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 *
 */
class Mageflow_Connect_Block_Adminhtml_Pullgrid_Grid_Column_Renderer
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        return $row->getContent();
    }

}
