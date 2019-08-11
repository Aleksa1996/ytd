<?php

namespace App\Classes;

class WebsocketClient
{
    private $host;
    private $port;

    private $httpClient;

    public function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;
        $this->httpClient =  new \Swoole\Coroutine\http\Client($this->host, $this->port);
        $this->setDefaultsHttpClient();
    }

    private function setDefaultsHttpClient()
    {
        // set timeout
        $this->httpClient->set([
            'timeout' => 1
        ]);
        // upgrade to websocket
        $this->httpClient->upgrade('/socket.io/?EIO=3&transport=websocket');
    }

    public function push($event, $message)
    {
        $encodedMessage = $this->encodeMessage($event, $message);
        $this->httpClient->push($encodedMessage);
    }

    private function encodeMessage($event, $data)
    {
        return json_encode([$event, $data]);
    }
}
