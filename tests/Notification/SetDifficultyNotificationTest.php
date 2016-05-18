<?php

namespace BitWasp\Stratum\Tests\Notification;

use BitWasp\Stratum\Api\MiningClient;
use BitWasp\Stratum\Notification\SetDifficultyNotification;
use BitWasp\Stratum\Tests\AbstractStratumTest;

class SetDifficultyNotificationTest extends AbstractStratumTest
{
    public function testCreate()
    {
        $diff = 1;
        $note = new SetDifficultyNotification($diff);
        $this->assertEquals($diff, $note->getDifficulty());
    }

    public function testToRequest()
    {
        $diff = 1;
        $note = new SetDifficultyNotification($diff);
        $request = $note->toRequest();
        $this->assertEquals(null, $request->getId());
        $this->assertEquals(MiningClient::SET_DIFFICULTY, $request->getMethod());
        $this->assertEquals([$diff], $request->getParams());
    }
}
