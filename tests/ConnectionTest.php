<?php

namespace BitWasp\Stratum\Tests;

use BitWasp\Stratum\Api\ElectrumClient;
use BitWasp\Stratum\Api\MiningClient;
use BitWasp\Stratum\Client;
use BitWasp\Stratum\Connection;
use BitWasp\Stratum\Notification\NotificationInterface;
use BitWasp\Stratum\Notification\NumBlocksNotification;
use BitWasp\Stratum\Request\Request;
use BitWasp\Stratum\Request\RequestFactory;
use BitWasp\Stratum\Request\Response;
use React\EventLoop\StreamSelectLoop;
use React\Promise\Deferred;
use React\Socket\Server;
use React\Socket\Connection as SocketConnection;
use React\SocketClient\TcpConnector;
use React\Stream\Stream;

class ConnectionTest extends AbstractStratumTest
{
    public function getMockStream()
    {
        return $this->getMockBuilder('\React\Stream\Stream')
            ->disableOriginalConstructor()
            ->setMethods(['write', 'create', 'close'])
            ->getMock();
    }

    public function testSend()
    {
        $sentData = new Deferred();
        $sentData->promise()->then(function () {
            $this->assertEquals('test', func_get_args()[0]);
        }, function () {
            $this->fail();
        });

        $stream = $this->getMockStream();
        $stream->expects($this->once())
            ->method('write')
            ->willReturnCallback(function () use (&$sentData) {
                $args = func_get_args();
                if (isset($args[0])) {
                    $sentData->resolve($args[0]);
                } else {
                    $sentData->reject();
                }
            });

        $factory = new RequestFactory();
        $connection = new Connection($stream, $factory);
        $connection->sendData('test');
    }

    public function testNotify()
    {
        $note = new NumBlocksNotification(392312);

        $serialized = new Deferred();
        $serialized->promise()->then(function () use ($note) {
            $this->assertEquals($note->toRequest()->write(), func_get_args()[0]);
        }, function () {
            $this->fail();
        });

        $stream = $this->getMockStream();
        $stream->expects($this->once())
            ->method('write')
            ->willReturnCallback(function () use ($note, &$serialized) {
                $args = func_get_args();
                if (isset($args[0])) {
                    $serialized->resolve($args[0]);
                } else {
                    $serialized->reject();
                }
            });

        $factory = new RequestFactory();
        $connection = new Connection($stream, $factory);
        $connection->sendNotify($note);
    }

    public function testRequest()
    {
        $expected = new Request(null, 'test.method', [123]);

        $serialized = new Deferred();
        $serialized->promise()->then(function (Request $request) use ($expected) {
            $this->assertEquals($expected->getMethod(), $request->getMethod());
            $this->assertEquals($expected->getParams(), $request->getParams());
            $this->assertEquals($expected->write(), func_get_args()[0]);
        }, function ($e) {
            $this->fail($e);
        });

        $stream = $this->getMockStream();
        $stream->expects($this->once())
            ->method('write')
            ->willReturnCallback(function () use (&$serialized) {
                $args = func_get_args();
                if (isset($args[0])) {
                    $serialized->resolve($args[0]);
                } else {
                    $serialized->reject();
                }
            });

        $factory = new RequestFactory();
        $connection = new Connection($stream, $factory);
        $connection->request('test.method', [123]);
    }

    public function testClose()
    {
        $stream = $this->getMockStream();
        $stream->expects($this->once())->method('close');

        $factory = new RequestFactory();
        $connection = new Connection($stream, $factory);
        $connection->close();
    }

    public function getNotificationVectors()
    {
        return [
            ["id"=> null, "method" => ElectrumClient::ADDRESS_SUBSCRIBE, "params" => ['1NS17iag9jJgTHD1VXjvLCEnZuQ3rJDE9L', '690ce08a148447f482eb3a74d714f30a6d4fe06a918a0893d823fd4aca4df580']],
            ["id"=> null, "method" => ElectrumClient::NUMBLOCKS_SUBSCRIBE, "params" => [1]],
            ["id"=> null, "method" => MiningClient::SET_DIFFICULTY, "params" => [1]],
            ["id"=> null, "method" => ElectrumClient::HEADERS_SUBSCRIBE, "params" => [array_values(json_decode('{"nonce":
3355909169, "prev_block_hash":
"00000000000000002b3ef284c2c754ab6e6abc40a0e31a974f966d8a2b4d5206",
"timestamp": 1408252887, "merkle_root":
"6d979a3d8d0f8757ed96adcd4781b9707cc192824e398679833abcb2afdf8d73",
"block_height": 316023, "utxo_root":
"4220a1a3ed99d2621c397c742e81c95be054c81078d7eeb34736e2cdd7506a03",
"version": 2, "bits": 406305378}', true))]],
            ["id"=> null, "method" => MiningClient::NOTIFY, "params" => json_decode('["bf", "4d16b6f85af6e2198f44ae2a6de67f78487ae5611b77c6c0440b921e00000000",
"01000000010000000000000000000000000000000000000000000000000000000000000000ffffffff20020862062f503253482f04b8864e5008",
"072f736c7573682f000000000100f2052a010000001976a914d23fcdf86f7e756a64a7a9688ef9903327048ed988ac00000000", [],
"00000002", "1c2ac4af", "504e86b9", false]', true)]
        ];
    }

    /**
     * @param string $id
     * @param string $method
     * @param array $params
     * @throws \React\Socket\ConnectionException
     * @dataProvider getNotificationVectors
     */
    public function testDataTriggersNotification($id, $method, $params)
    {
        $event = $method;

        $array = ["id"=> $id, "method" => $method, "params" => $params];
        $data = json_encode($array)."\n";

        $loop = new StreamSelectLoop();
        $request = new RequestFactory();

        $server = new Server($loop);
        $server->on('connection', function (SocketConnection $connection) use ($data, $server) {
            $connection->write($data);
            $connection->on('close', function () use ($server) {
                $server->shutdown();
            });
        });
        $server->listen(54323, '127.0.0.1');

        $deferred = new Deferred();
        $deferred->promise()->then(function (NotificationInterface $note) use ($data, $server) {
            $value = $note->toRequest();
            $this->assertEquals($data['method'], $value->getMethod());
            $this->assertEquals($data['params'], $value->getParams());
        });

        $tcp = new TcpConnector($loop);
        $client = new Client($tcp, $request);
        $client->connect('127.0.0.1', 54323)->then(function (Connection $connection) use ($event, $deferred, $loop) {
            $connection->on($event, function (NotificationInterface $request) use ($deferred, $connection) {
                $deferred->resolve($request);
                $connection->close();
            });
        });

        $loop->run();
    }

    public function testReturnsResponse()
    {
        $loop = new StreamSelectLoop();
        $request = new RequestFactory();

        $server = new Server($loop);
        $server->on('connection', function (SocketConnection $connection) use ($server, $request) {
            $connection->on('data', function ($data) use ($connection, $request) {
                $req = $request->response($data);
                $response = new Response($req->getId(), ['1.0']);
                $connection->write($response->write());
            });

            $connection->on('close', function () use ($server) {
                $server->shutdown();
            });
        });

        $server->listen(54323, '127.0.0.1');

        $tcp = new TcpConnector($loop);
        $client = new Client($tcp, $request);
        $client->connect('127.0.0.1', 54323)->then(function (Connection $connection) use ($loop) {
            $deferred = new Deferred();
            $deferred->promise()->then(function ($value) {
                $this->assertEquals(1, $value);
            });

            $electrum = new ElectrumClient($connection);
            $electrum->getServerVersion('1.9.6', ' 0.6')->then(function () use ($deferred, $connection) {
                $deferred->resolve(1);
                $connection->close();
            }, function () use ($loop) {
                $loop->stop();
                $this->fail();
            });
        });

        $loop->run();
    }

    public function getNotificationFailureVectors()
    {
        return [
            [null, ElectrumClient::ADDRESS_SUBSCRIBE, []],
            [null, ElectrumClient::ADDRESS_SUBSCRIBE, ['1NS17iag9jJgTHD1VXjvLCEnZuQ3rJDE9L']],
            [null, ElectrumClient::NUMBLOCKS_SUBSCRIBE, []],
            [null, ElectrumClient::HEADERS_SUBSCRIBE, []],
            [null, ElectrumClient::HEADERS_SUBSCRIBE, [array_fill(0, 7, 0)]],
            [null, MiningClient::NOTIFY, [array_fill(0, 8, 0)]],
            [null, MiningClient::SET_DIFFICULTY, []],
        ];
    }

    /**
     * @param $id
     * @param $method
     * @param $params
     * @expectedException \RuntimeException
     * @dataProvider getNotificationFailureVectors
     */
    public function testDataNotificationFailures($id, $method, $params)
    {
        $array = ["id"=> $id, "method" => $method, "params" => $params];
        $data = json_encode($array)."\n";
        
        $request = new RequestFactory();

        $loop = new StreamSelectLoop();
        $connection = new Connection(new Stream(fopen('php://stdin', 'r+'), $loop), $request);
        $connection->onMessage($data);
    }

    public function testOnDataWorksWithMultilineMessages()
    {
        $data = json_encode([
            "id"=> 1, "result" => [1]
        ])."\n".
            json_encode([
                "id"=> 2, "result" => [2]
            ])."\n";

        $loop = new StreamSelectLoop();
        $request = new RequestFactory();
        $connection = new Connection(new Stream(fopen('php://stdin', 'r'), $loop), $request);
        $counter = 0;
        $connection->on('message', function () use (&$counter) {
            $counter++;
        });
        $connection->onData($data);
        $this->assertEquals(2, $counter);
    }

    public function testOnDataWorksWithPartialMessages()
    {
        $data = json_encode([
                "id"=> 1, "result" => [1]
            ])."\n";
        $half1 = substr($data, 0, 4);
        $half2 = substr($data, 4);

        $loop = new StreamSelectLoop();
        $request = new RequestFactory();
        $connection = new Connection(new Stream(fopen('php://stdin', 'r'), $loop), $request);
        $counter = 0;
        $connection->on('message', function () use (&$counter) {
            $counter++;
        });

        $connection->onData($half1);
        $connection->onData($half2);
        $this->assertEquals(1, $counter);
    }
}
