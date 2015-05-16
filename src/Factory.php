<?php

namespace BitWasp\Stratum;

use BitWasp\Stratum\Request\RequestFactory;
use React\EventLoop\LoopInterface;
use React\SocketClient\Connector;

class Factory
{
    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var Connector
     */
    private $connector;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @param LoopInterface $loop
     * @param Connector $connector
     * @param RequestFactory $requestFactory
     */
    public function __construct(LoopInterface $loop, Connector $connector, RequestFactory $requestFactory)
    {
        $this->loop = $loop;
        $this->connector = $connector;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @param $host
     * @param $port
     * @param $timeout
     * @return Client
     */
    public function create($host, $port, $timeout = 5)
    {
        $executor = $this->createExecutor($host, $port, $timeout);

        return new Client($executor, $this->requestFactory);
    }

    /**
     * @param $host
     * @param $port
     * @param int $timeout
     * @return Executor
     */
    public function createExecutor($host, $port, $timeout = 5)
    {
        return new Executor($this->loop, $this->connector, $this->requestFactory, $host, $port, $timeout);
    }
}
