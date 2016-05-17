<?php

namespace BitWasp\Stratum\Tests\Notification;

use BitWasp\Stratum\Api\ElectrumClient;
use BitWasp\Stratum\Notification\NumBlocksNotification;
use BitWasp\Stratum\Tests\AbstractStratumTest;

class NumBlocksNotificationTest extends AbstractStratumTest
{
    public function testCreate()
    {
        $height = 123123;
        $note = new NumBlocksNotification($height);
        $this->assertEquals($height, $note->getHeight());
    }

    public function testToRequest()
    {
        $height = 123123;
        $note = new NumBlocksNotification($height);
        $request = $note->toRequest();
        $this->assertEquals(null, $request->getId());
        $this->assertEquals(ElectrumClient::NUMBLOCKS_SUBSCRIBE, $request->getMethod());
        $this->assertEquals([$height], $request->getParams());
    }
}
