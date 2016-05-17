<?php

namespace BitWasp\Stratum\Api;

use BitWasp\Stratum\Connection;

class ElectrumClient
{
    const SERVER_BANNER = 'server.banner';
    const SERVER_VERSION = 'server.version';
    const SERVER_DONATION_ADDR = 'server.donation_address';
    const SERVER_PEERS_SUBSCRIBE = 'server.peers.subscribe';
    const NUMBLOCKS_SUBSCRIBE = 'blockchain.numblocks.subscribe';
    const TRANSACTION_BROADCAST = 'blockchain.transaction.broadcast';
    const HEADERS_SUBSCRIBE = 'blockchain.headers.subscribe';
    const TRANSACTION_GET_MERKLE = 'blockchain.transaction.get_merkle';
    const TRANSACTION_GET = 'blockchain.transaction.get';
    const ADDRESS_SUBSCRIBE = 'blockchain.address.subscribe';
    const ADDRESS_GET_HISTORY = 'blockchain.address.get_history';
    const ADDRESS_GET_BALANCE = 'blockchain.address.get_balance';
    const ADDRESS_GET_PROOF = 'blockchain.address.get_proof';
    const ADDRESS_GET_MEMPOOL = 'blockchain.address.get_mempool';
    const ADDRESS_LIST_UNSPENT = 'blockchain.address.listunspent';
    const ESTIMATE_FEE = 'blockchain.estimatefee';
    const RELAY_FEE = 'blockchain.relayfee';
    const UTXO_GET_ADDRESS = 'blockchain.utxo.get_address';
    const BLOCK_GET_HEADER = 'blockchain.block.get_header';
    const BLOCK_GET_CHUNK = 'blockchain.block.get_chunk';

    /**
     * @var Connection
     */
    private $conn;

    /**
     * ElectrumServer constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->conn = $connection;
    }

    /**
     * @param string $clientVersion
     * @param string $protocolVersion
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function getServerVersion($clientVersion, $protocolVersion)
    {
        return $this->conn->request(self::SERVER_VERSION, [$clientVersion, $protocolVersion]);
    }

    /**
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function getServerBanner()
    {
        return $this->conn->request(self::SERVER_BANNER);
    }

    /**
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function getDonationAddress()
    {
        return $this->conn->request(self::SERVER_DONATION_ADDR);
    }

    /**
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function getServerPeers()
    {
        return $this->conn->request(self::SERVER_PEERS_SUBSCRIBE);
    }

    /**
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function subscribeNumBlocks()
    {
        return $this->conn->request(self::NUMBLOCKS_SUBSCRIBE);
    }

    /**
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function subscribeHeaders()
    {
        return $this->conn->request(self::HEADERS_SUBSCRIBE);
    }

    /**
     * @param string $address
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function subscribeAddress($address)
    {
        return $this->conn->request(self::ADDRESS_SUBSCRIBE, [$address]);
    }

    /**
     * @param string $hex
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function transactionBroadcast($hex)
    {
        return $this->conn->request(self::TRANSACTION_BROADCAST, [$hex]);
    }

    /**
     * @param string $txid
     * @param int $height
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function transactionGetMerkle($txid, $height)
    {
        return $this->conn->request(self::TRANSACTION_GET_MERKLE, [$txid, $height]);
    }

    /**
     * @param string $txid
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function transactionGet($txid)
    {
        return $this->conn->request(self::TRANSACTION_GET, [$txid]);
    }

    /**
     * @param string $address
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function addressGetHistory($address)
    {
        return $this->conn->request(self::ADDRESS_GET_HISTORY, [$address]);
    }

    /**
     * @param string $address
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function addressGetBalance($address)
    {
        return $this->conn->request(self::ADDRESS_GET_BALANCE, [$address]);
    }

    /**
     * @param string $address
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function addressGetProof($address)
    {
        return $this->conn->request(self::ADDRESS_GET_PROOF, [$address]);
    }

    /**
     * @param string $address
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function addressListUnspent($address)
    {
        return $this->conn->request(self::ADDRESS_LIST_UNSPENT, [$address]);
    }

    /**
     * @param string $address
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function addressGetMempool($address)
    {
        return $this->conn->request(self::ADDRESS_GET_MEMPOOL, [$address]);
    }

    /**
     * @param string $txid
     * @param int $nOutput
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function utxoGetAddress($txid, $nOutput)
    {
        return $this->conn->request(self::UTXO_GET_ADDRESS, [$txid, $nOutput]);
    }

    /**
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function estimateFee()
    {
        return $this->conn->request(self::ESTIMATE_FEE);
    }

    /**
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function relayFee()
    {
        return $this->conn->request(self::RELAY_FEE);
    }

    /**
     * @param int $blockHeight
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function blockGetHeader($blockHeight)
    {
        return $this->conn->request(self::BLOCK_GET_HEADER, [$blockHeight]);
    }

    /**
     * @param int $blockHeight
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function blockGetChunk($blockHeight)
    {
        return $this->conn->request(self::BLOCK_GET_CHUNK, [$blockHeight]);
    }
}
