<?php

namespace BitWasp\Stratum\Tests\Notification;

use BitWasp\Stratum\Api\ElectrumClient;
use BitWasp\Stratum\Notification\AddressNotification;
use BitWasp\Stratum\Tests\AbstractStratumTest;

class AddressNotificationTest extends AbstractStratumTest
{
    public function testCreate()
    {
        $address = '1NS17iag9jJgTHD1VXjvLCEnZuQ3rJDE9L';
        $txid = '690ce08a148447f482eb3a74d714f30a6d4fe06a918a0893d823fd4aca4df580';

        $note = new AddressNotification($address, $txid);

        $this->assertEquals($address, $note->getAddress());
        $this->assertEquals($txid, $note->getTxid());
    }

    public function testToRequest()
    {
        $address = '1NS17iag9jJgTHD1VXjvLCEnZuQ3rJDE9L';
        $txid = '690ce08a148447f482eb3a74d714f30a6d4fe06a918a0893d823fd4aca4df580';

        $note = new AddressNotification($address, $txid);

        $request = $note->toRequest();
        $this->assertEquals(null, $request->getId());
        $this->assertEquals(ElectrumClient::ADDRESS_SUBSCRIBE, $request->getMethod());
        $this->assertEquals([$address, $txid], $request->getParams());
    }
}
