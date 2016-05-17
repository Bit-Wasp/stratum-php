<?php

namespace BitWasp\Stratum\Tests\Request;

use BitWasp\Stratum\Exceptions\ApiError;
use BitWasp\Stratum\Request\RequestFactory;
use BitWasp\Stratum\Tests\AbstractStratumTest;

class ErrorTest extends AbstractStratumTest
{
    public function testRequestFactoryParse()
    {
        $factory = new RequestFactory();

        $id = 909;
        $error = 'Unknown method';

        try {
            $factory->response(json_encode(['id' => 909, 'error' => $error]));
        } catch (ApiError $e) {
            $this->assertEquals($id, $e->getId());
            $this->assertEquals($error, $e->getMessage());

            $written = json_encode(['id' => $id, 'error' =>$error])."\n";
            $this->assertEquals($written, $e->write());
        } catch (\Exception $e) {
            $this->fail('wrong case was triggered - not too bad, just make sure the equivalent tests are run');
        }
    }
}
