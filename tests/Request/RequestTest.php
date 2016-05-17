<?php

namespace BitWasp\Stratum\Tests\Request;

use BitWasp\Stratum\Request\Request;
use BitWasp\Stratum\Request\RequestFactory;
use BitWasp\Stratum\Tests\AbstractStratumTest;

class RequestTest extends AbstractStratumTest
{
    public function testRequest()
    {
        $id = 909;
        $method = 'service.help';
        $params = ['a','b','c'];
        $request = new Request($id, $method, $params);

        $this->assertEquals($id, $request->getId());
        $this->assertEquals($method, $request->getMethod());
        $this->assertEquals($params, $request->getParams());

        $written = json_encode(["json-rpc" => "2.0", "id" => $id, "method" => $method, "params" => $params]) . "\n";
        $this->assertEquals($written, $request->write());
    }

    public function testRequestFactoryCreate()
    {
        $factory = new RequestFactory();

        $method = 'service.help';
        $params = ['a','b','c'];
        $request = $factory->create($method, $params);

        $this->assertEquals($method, $request->getMethod());
        $this->assertEquals($params, $request->getParams());
    }

    public function testRequestFactoryParse()
    {
        $factory = new RequestFactory();

        $id = 909;
        $method = 'this.method';
        $params = ['a','b','c'];

        /** @var Request $request */
        $request = $factory->response(json_encode(['id'=>909, 'method' => $method, 'params' => $params]));

        $this->assertInstanceOf('BitWasp\Stratum\Request\Request', $request);
        $this->assertEquals($id, $request->getId());
        $this->assertEquals($method, $request->getMethod());
        $this->assertEquals($params, $request->getParams());
    }
}
