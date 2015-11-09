<?php

namespace Transmission\Clients;


/**
 * Class HttpRequestClient
 * @package Playground\Transmission\Clients
 */
class HttpRequestClient extends ClientAbstract
{
    /**
     * @var \HttpRequestPool
     */
    protected $client;


    /**
     * @param $host
     */
    public function __construct(
        $host = ""
    )
    {
        if ($host != "")
            $this->setTransmissionURL($host);

        $this->setVendorClient(new \HttpRequestPool());
    }

    public function request(array $params)
    {
        $request = new \HttpRequest($this->getTransmissionURL(), "POST");
        $request->addBody(json_encode($params));
        $this->client->attach($request);
        return $this->client->send();
    }


}