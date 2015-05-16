<?php

namespace BitWasp\Stratum;

use BitWasp\Stratum\Request\Request;
use BitWasp\Stratum\Request\RequestFactory;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\SocketClient\ConnectorInterface;
use React\Stream\Stream;

class Executor
{

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var ConnectorInterface
     */
    private $connector;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int|string
     */
    private $port;

    /**
     * @var int
     */
    private $timeout;

    /**
     * @param LoopInterface $loop
     * @param $host
     * @param $port
     */
    public function __construct(LoopInterface $loop, ConnectorInterface $connector, RequestFactory $requestFactory, $host, $port, $timeout = 5)
    {
        $this->loop = $loop;
        $this->requestFactory = $requestFactory;
        $this->connector = $connector;
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    /**
     * @param Request $request
     * @return \React\Promise\Promise
     */
    public function query(Request $request)
    {
        $deferred = new Deferred();

        $this->createConnection()->then(function (Stream $stream) use ($deferred, $request, &$steamBuffer) {
            $streamBuffer = '';
            $stream->on('data', function ($data) use ($deferred, $stream, $request, &$streamBuffer) {
                if (substr($data, strlen($data) - 1) == "\n") {
                    $stream->close();
                    $deferred->resolve($this->requestFactory->response($streamBuffer . $data));
                } else {
                    $streamBuffer .= $data;
                    $deferred->notify('Received partial response');
                }
                return;
            });

            $stream->write($request->write());
        }, function () use ($deferred) {
            $deferred->reject(new \ErrorException('Failed to connect to host'));
        });

        return $deferred->promise();
    }

    /**
     * @return \React\Promise\Promise
     */
    protected function createConnection()
    {
        return $this->connector->create($this->host, $this->port);
    }
}
