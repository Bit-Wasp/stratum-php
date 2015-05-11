<?php

namespace BitWasp\Bitcoin\Stratum\Connector;

use BitWasp\Bitcoin\Stratum\Request\Request;
use React\Promise\Deferred;
use React\SocketClient\Connector;

class Tcp implements ConnectorInterface
{
    /**
     * @var Connector
     */
    private $connector;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @param Connector $connector
     * @param $host
     * @param $port
     */
    public function __construct(Connector $connector, $host, $port)
    {
        $this->connector = $connector;
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @param $response
     * @return bool
     */
    public function isWellFormedResponse($response)
    {
        return substr($response, strlen($response) - 1) == "\n";
    }

    /**
     * @param Request $request
     * @return \React\Promise\Promise
     */
    public function send(Request $request)
    {
        $deferred = new Deferred();

        $this->connector->create($this->host, $this->port)->then(
            function (\React\Stream\Stream $stream) use ($deferred, $request) {
                $streamBuffer = '';
                $stream->on('data', function ($response) use ($stream, $deferred, &$streamBuffer) {
                    if ($this->isWellFormedResponse($response)) {
                        $stream->close();
                        $deferred->resolve($streamBuffer . $response);
                    } else {
                        $streamBuffer .= $response;
                    }
                });

                $stream->write($request->write());
            }
        );

        return $deferred->promise();
    }
}
