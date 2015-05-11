<?php

namespace BitWasp\Bitcoin\Stratum\Tests;

use BitWasp\Bitcoin\Stratum\Request\Request;
use BitWasp\Bitcoin\Stratum\Request\RequestFactory;
use BitWasp\Bitcoin\Stratum\StratumClient;

class StratumClientTest extends AbstractStratumTest
{
    public function getConnector()
    {
        $loop = \React\EventLoop\Factory::create();

        $dnsResolverFactory = new \React\Dns\Resolver\Factory();
        $dns = $dnsResolverFactory->create('8.8.8.8', $loop);

        return new \React\SocketClient\Connector($loop, $dns);
    }

    public function getConnectorMock()
    {
        return $this->getMock('BitWasp\Bitcoin\Stratum\Connector\ConnectorInterface');
    }

    public function testStratumClient()
    {
        $requestFactory = new RequestFactory();

        $string = '';
        $mock = $this->getConnectorMock();
        $mock->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf('BitWasp\Bitcoin\Stratum\Request\Request'))
            ->willReturnCallback(
                function () use ($string) {
                    return \React\Promise\resolve($string);
                }
            );

        $stratum = new StratumClient($mock, $requestFactory);
        $result = $stratum->request('server.banner');
        $result->then(function ($value) use ($string) {
            $this->assertEquals($string, $value);
        });
    }
}
