<?php

namespace BitWasp\Stratum;

use BitWasp\Stratum\Request\RequestFactory;
use React\SocketClient\ConnectorInterface;
use React\Stream\Stream;

class Client
{
    /**
     * @var ConnectorInterface
     */
    private $connector;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * Client constructor.
     * @param ConnectorInterface $connector
     * @param RequestFactory $requestFactory
     */
    public function __construct(ConnectorInterface $connector, RequestFactory $requestFactory)
    {
        $this->connector = $connector;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @param string $host
     * @param int $port
     * @return string
     */
    public function connect($host, $port)
    {
        $uri = $host.':'.$port;
        return $this->connector->connect($uri)->then(function (Stream $stream) {
            return new Connection($stream, $this->requestFactory);
        }, function (\Exception $e) {
            throw $e;
        });
    }
}
