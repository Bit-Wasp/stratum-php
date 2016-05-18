<?php

namespace BitWasp\Stratum\Tests\Api;

use BitWasp\Stratum\Api\MiningClient;
use BitWasp\Stratum\Request\Request;
use BitWasp\Stratum\Tests\AbstractStratumTest;
use React\Promise\FulfilledPromise;

class MiningClientTest extends AbstractStratumTest
{
    public function getMethodVectors()
    {
        return [
            ['subscribeMining', MiningClient::MINING_SUBSCRIBE, []],
            ['authorize', MiningClient::AUTHORIZE, ['abc', 'abc']],
            ['submit', MiningClient::SUBMIT, [1, 2, 3, 4, 5]],
        ];
    }

    /**
     * @dataProvider getMethodVectors
     */
    public function testMethod($method, $stratumMethod, array $params)
    {
        $conn = $this->getMockBuilder('\BitWasp\Stratum\Connection')
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $conn->expects($this->once())->method('request')
            ->with($stratumMethod, $params)
            ->willReturn(new FulfilledPromise(new Request(1, $stratumMethod, $params)));

        $server = new MiningClient($conn);

        $result = call_user_func_array([$server, $method], $params);
        $result->then(function (Request $request) use ($stratumMethod) {

        }, function () {
            $this->fail();
        });

    }
}
