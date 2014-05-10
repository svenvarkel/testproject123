<?php

/**
 * Connectbutton.php
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
 * Mageflow_Connect_Block_System_Config_Api_Connectbutton
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Sven Varkel <sven@mageflow.com>
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_System_Config_Api_Connectbutton
    extends Mageflow_Connect_Block_System_Config_Api_Basebutton
{

    /**
     * Creates "connect to api" button
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
                    "Connect MageFlow API"
                ),
//            'onclick' => "console.log('test')",
            'class'        => 'disabled',
            'comment'      => "",
            'id'           => "btn_connect",
            'data-api-url' => Mage::helper("adminhtml")->getUrl(
                    'mageflow_connect/ajax/getcompanies'
                ) . '?isAjax=true',
//            'onclick'      => sprintf(
//                "if(!jQuery(this).hasClass('disabled'))
//                jQuery.ajax('%s', {type:'GET', data:mageflow.getCredentials(),
//                 success:function(response){var e=
//                 new jQuery.Event('populate_company_select');
//                 e.custom_data=response; jQuery(document).trigger(e);}})",
//                Mage::helper("adminhtml")->getUrl(
//                    'mageflow_connect/ajax/getcompanies'
//                ) . '?isAjax=true'
//            ),
            'onclick'      => 'javascript:;',
            'after_html'   => (Mage::getStoreConfig(
                    'mageflow_connect/general/api/is_connected'
                ) ? '' : ''),
            'before_html'  => ''
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
        <div style="margin-top:5px;">
Please follow the steps below to connect this Magento instance to MageFlow:
<ol style="list-style: decimal inside;">
<li>Enable the API connection.</li>
<li>Enter your API keys - available under the Profile of your MageFlow account (sign in at https://app.mageflow.com).</li>
<li>Click the "Save Config" button (top right).</li>
<li>Click the "Connect MageFlow API" button to get a list of your available MageFlow company accounts.</li>
<li>Select the MageFlow company and project to connect this Magento instance to.</li>
<li>Click the "Register instance" button to get the unique MageFlow key for this Magento instance.</li>
<li>Click the "Set up OAuth" button to set up the connection.</li>
</ol>
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
            <tr id="row_connect">
                <td>&nbsp;</td>
                <td>
                    <div style="margin-top:5px;">
                        Select the MageFlow company account to get a list of the available projects.
                    </div>

                </td>
            </tr>
HTML;

        return $html;
    }

    protected function getSignupUrl()
    {
        return "https://app.mageflow.com/";
    }

}
