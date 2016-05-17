<?php

namespace BitWasp\Stratum\Tests;

use BitWasp\Stratum\Client;
use BitWasp\Stratum\Request\RequestFactory;
use React\EventLoop\StreamSelectLoop;
use React\Promise\Deferred;
use React\Socket\Server;
use React\SocketClient\TcpConnector;
use React\Stream\Stream;

class ClientTest extends AbstractStratumTest
{
    public function testConnect()
    {
        $loop = new StreamSelectLoop();
        $deferred = new Deferred();
        $deferred->promise()->then(function ($value) use ($loop) {
            $this->assertEquals(1, $value);
            $loop->stop();
        }, function () {
            $this->fail('Promise was rejected');
        });

        $server = new Server($loop);
        $server->listen(54321, '0.0.0.0');
        $server->on('connection', function (Stream $stream) use ($deferred, $server) {
            $deferred->resolve(1);
            $server->shutdown();
        });

        $request = new RequestFactory();
        $connector = new TcpConnector($loop);
        $client = new Client($connector, $request);
        $client->connect('127.0.0.1', 54321);

        $loop->run();
    }

    public function testConnectFails()
    {

        $loop = new StreamSelectLoop();
        $deferred = new Deferred();
        $deferred->promise()->then(function () {
            $this->fail('should not have succeeded');
        }, function ($value) use ($loop) {
            $this->assertEquals(1, $value);
            $loop->stop();
        });

        $request = new RequestFactory();
        $connector = new TcpConnector($loop);
        $client = new Client($connector, $request);
        $client->connect('127.0.0.1', 54320);

        $loop->run();
    }
}
