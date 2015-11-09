<?php

namespace Transmission\Tests;

use Transmission\Clients\GuzzleClient;
use Transmission\Transmission;

/**
 * Class TransmissionTest
 * */
class TransmissionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Transmission
     */
    public $transmission;

    /**
     * Creates instance of Transmission object
     */
    function setUp()
    {

        $this->transmission = new Transmission(new GuzzleClient());
    }

    function testGet()
    {
        $this->transmission->torrentGet();
    }

    function testFail()
    {
        $this->setExpectedException('GuzzleHttp\Exception\ConnectException','cURL error 7: Failed to connect to localhost port 9091: Connection refused (see http://curl.haxx.se/libcurl/c/libcurl-errors.html');
        $this->transmission->allGetFields();
    }

    /**
     * @param array $ids
     *
     * @depends testGet
     */
    function testActions($ids = [])
    {
        /*$this->transmission->torrentStart($ids);
        $this->transmission->torrentStartNow($ids);
        $this->transmission->torrentVerify($ids);
        $this->transmission->torrentReannounce($ids);
        $this->transmission->torrentStop($ids);*/
    }

    function testSet()
    {
    }

    function testAdd()
    {
    }

    function testRemove()
    {
    }

    function testSetLocation()
    {
    }

    function testRenamePath()
    {
    }
}