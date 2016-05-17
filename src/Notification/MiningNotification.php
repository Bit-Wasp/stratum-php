<?php

namespace BitWasp\Stratum\Notification;

use BitWasp\Stratum\Api\MiningClient;
use BitWasp\Stratum\Request\Request;

class MiningNotification implements NotificationInterface
{
    /**
     * @var string
     */
    private $job_id;

    /**
     * @var string
     */
    private $prevhash;

    /**
     * @var string
     */
    private $coinb1;

    /**
     * @var string
     */
    private $coinb2;

    /**
     * @var array
     */
    private $merkle_branch;

    /**
     * @var int
     */
    private $version;

    /**
     * @var int
     */
    private $nbits;

    /**
     * @var int
     */
    private $ntime;

    /**
     * @var bool
     */
    private $clean_jobs;

    /**
     * MiningNotification constructor.
     * @param string $job_id
     * @param string $prevhash
     * @param string $coinb1
     * @param string $coinb2
     * @param array $merkle_branch
     * @param int $version
     * @param int $bits
     * @param int $time
     * @param bool $cleanjobs
     */
    public function __construct($job_id, $prevhash, $coinb1, $coinb2, $merkle_branch, $version, $bits, $time, $cleanjobs)
    {
        $this->job_id = $job_id;
        $this->prevhash = $prevhash;
        $this->coinb1 = $coinb1;
        $this->coinb2 = $coinb2;
        $this->merkle_branch = $merkle_branch;
        $this->version = $version;
        $this->nbits = $bits;
        $this->ntime = $time;
        $this->clean_jobs = $cleanjobs;
    }

    /**
     * @return string
     */
    public function getJobId()
    {
        return $this->job_id;
    }

    /**
     * @return string
     */
    public function getPrevhash()
    {
        return $this->prevhash;
    }

    /**
     * @return string
     */
    public function getCoinb1()
    {
        return $this->coinb1;
    }

    /**
     * @return string
     */
    public function getCoinb2()
    {
        return $this->coinb2;
    }

    /**
     * @return array
     */
    public function getMerkleBranch()
    {
        return $this->merkle_branch;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return int
     */
    public function getBits()
    {
        return $this->nbits;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->ntime;
    }

    /**
     * @return boolean
     */
    public function isCleanJobs()
    {
        return $this->clean_jobs;
    }

    /**
     * @return Request
     */
    public function toRequest()
    {
        return new Request(null, MiningClient::MINING_SUBSCRIBE, [$this->job_id, $this->prevhash, $this->coinb1, $this->coinb2, $this->merkle_branch, $this->version, $this->nbits, $this->ntime, $this->clean_jobs]);
    }
}
