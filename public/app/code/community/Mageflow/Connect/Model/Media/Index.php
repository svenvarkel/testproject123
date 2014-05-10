<?php
/**
 * @package    Mageflow
 * @subpackage Connect
 */

/**
 * MageFlow Media Index holds list of WYSIWYG images
 *
 * PLEASE READ THIS SOFTWARE LICENSE AGREEMENT ("LICENSE") CAREFULLY
 * BEFORE USING THE SOFTWARE. BY USING THE SOFTWARE, YOU ARE AGREEING
 * TO BE BOUND BY THE TERMS OF THIS LICENSE.
 * IF YOU DO NOT AGREE TO THE TERMS OF THIS LICENSE, DO NOT USE THE SOFTWARE.
 *
 * Full text of this license is available @license
 *
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @version    1.0
 * @author     MageFlow
 * @copyright  2013 MageFlow http://mageflow.com/
 *
 * @package    Mageflow
 * @subpackage Connect
 * @category   MFX
 *
 * @method string getFilename()
 * @method string getBasename()
 * @method string getPath()
 * @method integer getMtime()
 * @method string getHash()
 * @method string getName()
 * @method string getShortName()
 * @method string getUrl()
 * @method integer getWidth()
 * @method integer getHeight()
 * @method string getThumbUrl()
 * @method string getType()
 * @method integer getSize()
 * @method datetime getCreatedAt()
 * @method datetime getUpdatedAt()
 * @method string getMfGuid()
 *
 * @method setFilename(string $value)
 * @method setBasename(string $value)
 * @method setPath(string $value)
 * @method setMtime(integer $value)
 * @method setHash(string $value)
 * @method setName(string $value)
 * @method setShortName(string $value)
 * @method setUrl(string $value)
 * @method setWidth(integer $value)
 * @method setHeight(integer $value)
 * @method setThumbUrl(string $value)
 * @method setType(string $value)
 * @method setSize(integer $value)
 * @method setCreatedAt(datetime $value)
 * @method setUpdatedAt(datetime $value)
 *
 */

class Mageflow_Connect_Model_Media_Index extends Mage_Core_Model_Abstract
{
    /**
     * Class constructor
     *
     * @return Item
     */
    public function _construct()
    {
        $this->_init('mageflow_connect/media_index');
        return parent::_construct();
    }


}