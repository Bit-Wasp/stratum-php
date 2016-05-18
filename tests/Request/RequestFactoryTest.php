<?php

namespace BitWasp\Stratum\Tests\Request;

use BitWasp\Stratum\Request\RequestFactory;
use BitWasp\Stratum\Tests\AbstractStratumTest;

class RequestFactoryTest extends AbstractStratumTest
{
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid JSON
     */
    public function testInvalidJson()
    {
        $factory = new RequestFactory();
        $factory->response('{');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Response missing error/params/result
     */
    public function testMalformedResponse()
    {
        $factory = new RequestFactory();
        $factory->response('{}');
    }
}
