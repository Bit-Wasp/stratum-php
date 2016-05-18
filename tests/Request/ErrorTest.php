<?php

namespace BitWasp\Stratum\Tests\Request;

use BitWasp\Stratum\Exception\ApiError;
use BitWasp\Stratum\Request\RequestFactory;
use BitWasp\Stratum\Tests\AbstractStratumTest;

class ErrorTest extends AbstractStratumTest
{
    public function testRequestFactoryParse()
    {
        $factory = new RequestFactory();

        $id = 909;
        $error = 'Unknown method';

        $e = $factory->response(json_encode(['id' => 909, 'error' => $error]));
        $this->assertEquals($id, $e->getId());
        $this->assertEquals($error, $e->getMessage());

        $written = json_encode(['id' => $id, 'error' =>$error])."\n";
        $this->assertEquals($written, $e->write());
    }
}
