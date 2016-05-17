<?php

namespace BitWasp\Stratum\Notification;

use BitWasp\Stratum\Api\ElectrumClient;
use BitWasp\Stratum\Request\Request;

class HeadersNotification implements NotificationInterface
{
    /**
     * @var int
     */
    private $version;

    /**
     * @var string
     */
    private $prevBlock;

    /**
     * @var string
     */
    private $merkleRoot;

    /**
     * @var int
     */
    private $timestamp;

    /**
     * @var int
     */
    private $bits;

    /**
     * @var int
     */
    private $nonce;

    /**
     * @var int
     */
    private $height;

    /**
     * @var string
     */
    private $utxoRoot;

    /**
     * HeadersNotification constructor.
     * @param int $nonce
     * @param string $prevBlock
     * @param int $timestamp
     * @param string $merkleroot
     * @param int $height
     * @param string $utxoRoot
     * @param int $version
     * @param int $bits
     */
    public function __construct($nonce, $prevBlock, $timestamp, $merkleroot, $height, $utxoRoot, $version, $bits)
    {
        $this->nonce = $nonce;
        $this->prevBlock = $prevBlock;
        $this->timestamp = $timestamp;
        $this->merkleRoot = $merkleroot;
        $this->height = $height;
        $this->utxoRoot = $utxoRoot;
        $this->version = $version;
        $this->bits = $bits;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getPrevBlock()
    {
        return $this->prevBlock;
    }

    /**
     * @return string
     */
    public function getMerkleRoot()
    {
        return $this->merkleRoot;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return int
     */
    public function getBits()
    {
        return $this->bits;
    }

    /**
     * @return int
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return string
     */
    public function getUtxoRoot()
    {
        return $this->utxoRoot;
    }

    /**
     * @return Request
     */
    public function toRequest()
    {
        return new Request(null, ElectrumClient::HEADERS_SUBSCRIBE, [[
            $this->nonce, $this->prevBlock, $this->timestamp,
            $this->merkleRoot, $this->height, $this->utxoRoot,
            $this->version, $this->bits
        ]]);
    }
}
