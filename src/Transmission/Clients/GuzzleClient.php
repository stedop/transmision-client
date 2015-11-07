<?php namespace Playground\Transmission\Clients;
use GuzzleHttp\Psr7\Request;

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
        $this->setVendorClient(new \GuzzleHttp\Client($options));
    }


    public function request($method, array $params)
    {
        $headers = [];
        if ($this->getXTransmissionSessionId() != "")
            $headers = [
                "X-Transmission-Session-Id" =>
                $this->getXTransmissionSessionId()
            ];
        $request = new Request($method, "", $headers, json_encode($params));
        // GET THE Transmission SESSION ID and set it for the client
        try {
            return $this->client->send($request)->getBody()->getContents();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $this->setXTransmissionSessionId(
                $response->getHeader("X-Transmission-Session-Id")
            );

            $request = new Request($method, "", ["X-Transmission-Session-Id" => $this->getXTransmissionSessionId()], json_encode($params));
            return $this->client->send($request)->getBody()->getContents();
        }
    }
}