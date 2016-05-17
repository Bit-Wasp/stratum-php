<?php

namespace BitWasp\Stratum\Tests\Request;

use BitWasp\Stratum\Request\RequestFactory;
use BitWasp\Stratum\Request\Response;
use BitWasp\Stratum\Tests\AbstractStratumTest;

class ResponseTest extends AbstractStratumTest
{
    public function testResponse()
    {
        $id = 909;
        $result = ['a','b','c'];
        $response = new Response($id, $result);

        $this->assertEquals($id, $response->getId());
        $this->assertEquals($result, $response->getResult());

        $written = json_encode(["id"=>$id, "result" => $result]) . "\n";
        $this->assertEquals($written, $response->write());
    }

    public function testRequestFactory()
    {
        $factory = new RequestFactory();

        $id = 909;
        $result = ['a','b','c'];
        $response = $factory->response(json_encode(['id'=>909, 'result' => $result]));

        $this->assertInstanceOf('BitWasp\Stratum\Request\Response', $response);
        $this->assertEquals($id, $response->getId());
        $this->assertEquals($result, $response->getResult());
    }
}
