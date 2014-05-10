<?php

/**
 * Testbutton.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_connect
 * @author     Urmas Lipso <urmas@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 *
 */

/**
 * Mageflow_Connect_Block_System_Config_Api_Testbutton
 *
 * @category   MFX
 * @package    Mageflow_connect
 * @author     Urmas Lipso <urmas@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 *
 */
class Mageflow_Connect_Block_System_Config_Api_Testbutton
    extends Mageflow_Connect_Block_System_Config_Api_Basebutton
{

    /**
     * Creates "register instance" button
     *
     * @param type $buttonBlock
     *
     * @return string
     */
    public function getButtonData($buttonBlock)
    {
        $afterHtml = $this->getAfterHtml();

        $data = array(
            'label'        => Mage::helper('mageflow_connect')->__(
                    "Test API"
                ),
            'class'        => '',
            'comment'      => 'Test MageFlow API',
            'id'           => "btn_apitest",
            'after_html'   => $this->getAfterHtml(),
            'before_html'  => '',
            'onclick'      => 'javascript:;',
            'data-api-url' => Mage::helper("adminhtml")
                    ->getUrl('mageflow_connect/ajax/testapi') . '?isAjax=true'

        );
        return $data;
    }

    /**
     * Returns HTML that is prepended to button
     *
     * @return string
     */
    protected function getBeforeHtml()
    {
        $html
            = <<<HTML
        <div style="    margin-top:5px;">
                Test MageFlow API status
        </div>
HTML;

        return $html;
    }

    /**
     * Returns HTML that is appended to button
     *
     * @return string
     */
    protected function getAfterHtml()
    {
        $link = $this->getSignupUrl();
        $html
            = <<<HTML
<div>
    <ul>
        <li>Connection status: <span id="api_test_status">n/a</span></li>
        <li>Name: <span id="api_test_name">n/a</span></li>
        <li>Email: <span id="api_test_email">n/a</span></li>
    </ul>
</div>

HTML;

        return $html;
    }

}