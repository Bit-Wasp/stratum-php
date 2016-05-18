<?php

namespace BitWasp\Stratum\Tests\Api;

use BitWasp\Stratum\Api\ElectrumClient;
use BitWasp\Stratum\Request\Request;
use BitWasp\Stratum\Tests\AbstractStratumTest;
use React\Promise\FulfilledPromise;

class ElectrumServerTest extends AbstractStratumTest
{
    public function getMethodVectors()
    {
        return [
            ['getServerBanner', ElectrumClient::SERVER_BANNER, []],
            ['getServerVersion', ElectrumClient::SERVER_VERSION, ['1.9.6', ' 0.6']],
            ['getServerPeers', ElectrumClient::SERVER_PEERS_SUBSCRIBE, []],
            ['getDonationAddress', ElectrumClient::SERVER_DONATION_ADDR, []],
            ['subscribeNumBlocks', ElectrumClient::NUMBLOCKS_SUBSCRIBE, []],
            ['subscribeHeaders', ElectrumClient::HEADERS_SUBSCRIBE, []],
            ['subscribeAddress', ElectrumClient::ADDRESS_SUBSCRIBE, ['1NS17iag9jJgTHD1VXjvLCEnZuQ3rJDE9L']],
            ['transactionBroadcast', ElectrumClient::TRANSACTION_BROADCAST, ['690ce08a148447f482eb3a74d714f30a6d4fe06a918a0893d823fd4aca4df580']],
            ['transactionGetMerkle', ElectrumClient::TRANSACTION_GET_MERKLE, ['690ce08a148447f482eb3a74d714f30a6d4fe06a918a0893d823fd4aca4df580', 123]],
            ['transactionGet', ElectrumClient::TRANSACTION_GET, ['690ce08a148447f482eb3a74d714f30a6d4fe06a918a0893d823fd4aca4df580']],
            ['addressGetHistory', ElectrumClient::ADDRESS_GET_HISTORY, ['1NS17iag9jJgTHD1VXjvLCEnZuQ3rJDE9L']],
            ['addressGetBalance', ElectrumClient::ADDRESS_GET_BALANCE, ['1NS17iag9jJgTHD1VXjvLCEnZuQ3rJDE9L']],
            ['addressGetProof', ElectrumClient::ADDRESS_GET_PROOF, ['1NS17iag9jJgTHD1VXjvLCEnZuQ3rJDE9L']],
            ['addressGetMempool', ElectrumClient::ADDRESS_GET_MEMPOOL, ['1NS17iag9jJgTHD1VXjvLCEnZuQ3rJDE9L']],
            ['addressListUnspent', ElectrumClient::ADDRESS_LIST_UNSPENT, ['1NS17iag9jJgTHD1VXjvLCEnZuQ3rJDE9L']],
            ['estimateFee', ElectrumClient::ESTIMATE_FEE, []],
            ['relayFee', ElectrumClient::RELAY_FEE, []],
            ['blockGetHeader', ElectrumClient::BLOCK_GET_HEADER, [1]],
            ['blockGetChunk', ElectrumClient::BLOCK_GET_CHUNK, [1]],
            ['utxoGetAddress', ElectrumClient::UTXO_GET_ADDRESS, ['690ce08a148447f482eb3a74d714f30a6d4fe06a918a0893d823fd4aca4df580', 0]],
        ];
    }

    /**
     * @dataProvider getMethodVectors
     */
    public function testMethod($method, $stratumMethod, array $params)
    {
        $conn = $this->getMockBuilder('\BitWasp\Stratum\Connection')
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $conn->expects($this->once())->method('request')
            ->with($stratumMethod, $params)
            ->willReturn(new FulfilledPromise(new Request(1, $stratumMethod, $params)));

        $server = new ElectrumClient($conn);

        $result = call_user_func_array([$server, $method], $params);
        $result->then(function (Request $request) use ($stratumMethod) {

        }, function () {
            $this->fail();
        });

    }
}
