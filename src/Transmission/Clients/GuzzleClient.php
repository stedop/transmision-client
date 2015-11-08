<?php

namespace Transmission\Clients;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Class GuzzleClient
 * @package Playground\Clients
 */
class GuzzleClient extends ClientAbstract
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @param $host
     * @param array $options
     */
    public function __construct(
        $host = "",
        array $options = [
            'http_error' => false
        ]
    )
    {
        if ($host != "")
            $this->setTransmissionURL($host);

        $options['base_uri'] = $this->getTransmissionURL();
        $this->setVendorClient(new Client($options));
    }

    /**
     * Sends a request with the client
     * @param array $params
     *
     * @return string
     */
    public function request(array $params)
    {
        $headers = [];
        if ($this->getXTransmissionSessionId() != "")
            $headers = $this->formatSessionHeader();

        $request = new Request("POST", "", $headers, json_encode($params));
        // GET THE Transmission SESSION ID and set it for the client
        try {
            return $this->client->send($request)->getBody()->getContents();
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $this->setXTransmissionSessionId(
                $response->getHeader("X-Transmission-Session-Id")
            );

            $request = new Request("POST", "", $this->formatSessionHeader(), json_encode($params));
            return $this->client->send($request)->getBody()->getContents();
        }
    }

    public function formatSessionHeader()
    {
        return ["X-Transmission-Session-Id" => $this->getXTransmissionSessionId()];
    }
}