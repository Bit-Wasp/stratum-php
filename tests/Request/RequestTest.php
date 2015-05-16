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
    }

    public function testRequestFactory()
    {
        $factory = new RequestFactory();

        $method = 'service.help';
        $params = ['a','b','c'];
        $request = $factory->create($method, $params);

        $this->assertEquals($method, $request->getMethod());
        $this->assertEquals($params, $request->getParams());
    }
}
