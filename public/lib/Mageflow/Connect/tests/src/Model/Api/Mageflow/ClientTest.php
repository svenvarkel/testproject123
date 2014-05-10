<?php
/**
 *
 * ClientTest.php
 *
 * @author  sven
 * @created 04/15/2014 20:47
 */

namespace Mageflow\Connect\Model\Api\Mageflow;


class ClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Client
     */
    protected $client;

    public function setUp()
    {
        $helper = \Mage::helper('mageflow_connect/oauth');
        $this->client = $helper->getApiClient();
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\Mageflow\Connect\Model\Api\Mageflow\Client', $this->client);
    }

    public function testNewGet()
    {
        $out = $this->client->get('/find/Instance/instance_key/flwin');
        print_r($out);
        echo PHP_EOL;
    }

    public function testNewPost()
    {
        $out = $this->client->post('/changeset', ['description'=>'blaah']);
        print_r($out);
        echo PHP_EOL;
    }

    public function testNewPut()
    {
        $out = $this->client->put('/changeset', ['description'=>'blaah', 'id'=>1]);
        print_r($out);
        echo PHP_EOL;
    }
}
