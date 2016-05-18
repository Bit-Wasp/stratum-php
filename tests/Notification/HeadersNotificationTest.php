<?php

namespace BitWasp\Stratum\Tests\Notification;

use BitWasp\Stratum\Api\ElectrumClient;
use BitWasp\Stratum\Notification\HeadersNotification;
use BitWasp\Stratum\Tests\AbstractStratumTest;

class HeadersNotificationTest extends AbstractStratumTest
{
    public function testCreate()
    {
        $sample = json_decode('{"nonce":
3355909169, "prev_block_hash":
"00000000000000002b3ef284c2c754ab6e6abc40a0e31a974f966d8a2b4d5206",
"timestamp": 1408252887, "merkle_root":
"6d979a3d8d0f8757ed96adcd4781b9707cc192824e398679833abcb2afdf8d73",
"block_height": 316023, "utxo_root":
"4220a1a3ed99d2621c397c742e81c95be054c81078d7eeb34736e2cdd7506a03",
"version": 2, "bits": 406305378}', true);

        $headers = new HeadersNotification(
            $sample['nonce'],
            $sample['prev_block_hash'],
            $sample['timestamp'],
            $sample['merkle_root'],
            $sample['block_height'],
            $sample['utxo_root'],
            $sample['version'],
            $sample['bits']
        );
        $this->assertEquals($sample['nonce'], $headers->getNonce());
        $this->assertEquals($sample['prev_block_hash'], $headers->getPrevBlock());
        $this->assertEquals($sample['timestamp'], $headers->getTimestamp());
        $this->assertEquals($sample['merkle_root'], $headers->getMerkleRoot());
        $this->assertEquals($sample['block_height'], $headers->getHeight());
        $this->assertEquals($sample['utxo_root'], $headers->getUtxoRoot());
        $this->assertEquals($sample['version'], $headers->getVersion());
        $this->assertEquals($sample['bits'], $headers->getBits());
    }


    public function testToRequest()
    {

        $sample = json_decode('{"nonce":
3355909169, "prev_block_hash":
"00000000000000002b3ef284c2c754ab6e6abc40a0e31a974f966d8a2b4d5206",
"timestamp": 1408252887, "merkle_root":
"6d979a3d8d0f8757ed96adcd4781b9707cc192824e398679833abcb2afdf8d73",
"block_height": 316023, "utxo_root":
"4220a1a3ed99d2621c397c742e81c95be054c81078d7eeb34736e2cdd7506a03",
"version": 2, "bits": 406305378}', true);
        $values = array_values($sample);

        $headers = new HeadersNotification(
            $sample['nonce'],
            $sample['prev_block_hash'],
            $sample['timestamp'],
            $sample['merkle_root'],
            $sample['block_height'],
            $sample['utxo_root'],
            $sample['version'],
            $sample['bits']
        );
        $request = $headers->toRequest();
        $this->assertEquals(null, $request->getId());
        $this->assertEquals(ElectrumClient::HEADERS_SUBSCRIBE, $request->getMethod());
        $this->assertEquals([$values], $request->getParams());
    }
}
