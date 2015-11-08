<?php

namespace Transmission\Clients;

/**
 * Class ClientAbstract
 * @package Playground\Clients
 * Abstract class so clients can be replaceable
 */
abstract class ClientAbstract {
    /**
     * @var
     */
    protected $client;
    /**
     * @var string
     */
    protected $transmissionURL = 'http://localhost:9091/transmission/rpc/';

    public $XTransmissionSessionId;

    /**
     * @param array $params
     * @return mixed
     */
    abstract public function request(array $params);

    /**
     * @return mixed
     */
    public function getVendorClient()
    {
        return $this->client;
    }

    /**
     * @param $client
     */
    public function setVendorClient($client)
    {
        $this->client = $client;
    }

    public function getTransmissionURL() {
        return $this->transmissionURL;
    }

    /**
     * @param $transmissionURL
     */
    public function setTransmissionURL($transmissionURL)
    {
        $this->transmissionURL = $transmissionURL;
    }

    /**
     * @return mixed
     */
    public function getXTransmissionSessionId()
    {
        return $this->XTransmissionSessionId;
    }

    /**
     * @param mixed $XTransmissionSessionId
     */
    public function setXTransmissionSessionId($XTransmissionSessionId)
    {
        $this->XTransmissionSessionId = $XTransmissionSessionId;
    }
}