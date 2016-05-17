<?php

namespace BitWasp\Stratum\Tests\Notification;

use BitWasp\Stratum\Api\MiningClient;
use BitWasp\Stratum\Notification\MiningNotification;
use BitWasp\Stratum\Tests\AbstractStratumTest;

class MiningNotificationTest extends AbstractStratumTest
{
    public function testCreate()
    {
        $sample = json_decode('["bf", "4d16b6f85af6e2198f44ae2a6de67f78487ae5611b77c6c0440b921e00000000",
"01000000010000000000000000000000000000000000000000000000000000000000000000ffffffff20020862062f503253482f04b8864e5008",
"072f736c7573682f000000000100f2052a010000001976a914d23fcdf86f7e756a64a7a9688ef9903327048ed988ac00000000", [],
"00000002", "1c2ac4af", "504e86b9", false]', true);

        $note = new MiningNotification($sample[0], $sample[1], $sample[2], $sample[3], $sample[4], $sample[5], $sample[6], $sample[7], $sample[8]);
        $this->assertEquals($sample[0], $note->getJobId());
        $this->assertEquals($sample[1], $note->getPrevhash());
        $this->assertEquals($sample[2], $note->getCoinb1());
        $this->assertEquals($sample[3], $note->getCoinb2());
        $this->assertEquals($sample[4], $note->getMerkleBranch());
        $this->assertEquals($sample[5], $note->getVersion());
        $this->assertEquals($sample[6], $note->getBits());
        $this->assertEquals($sample[7], $note->getTime());
        $this->assertEquals($sample[8], $note->isCleanJobs());
    }

    public function testToRequest()
    {
        $sample = json_decode('["bf", "4d16b6f85af6e2198f44ae2a6de67f78487ae5611b77c6c0440b921e00000000",
"01000000010000000000000000000000000000000000000000000000000000000000000000ffffffff20020862062f503253482f04b8864e5008",
"072f736c7573682f000000000100f2052a010000001976a914d23fcdf86f7e756a64a7a9688ef9903327048ed988ac00000000", [],
"00000002", "1c2ac4af", "504e86b9", false]', true);

        $note = new MiningNotification($sample[0], $sample[1], $sample[2], $sample[3], $sample[4], $sample[5], $sample[6], $sample[7], $sample[8]);
        $request = $note->toRequest();
        $this->assertEquals(null, $request->getId());
        $this->assertEquals(MiningClient::MINING_SUBSCRIBE, $request->getMethod());
        $this->assertEquals($sample, $request->getParams());
    }
}
