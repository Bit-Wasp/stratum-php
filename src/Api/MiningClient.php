<?php

namespace BitWasp\Stratum\Api;

use BitWasp\Stratum\Connection;

class MiningClient
{
    const MINING_SUBSCRIBE = 'mining.subscribe';
    const SET_DIFFICULTY = 'mining.set_difficulty';
    const NOTIFY = 'mining.notify';
    const AUTHORIZE = 'mining.authorize';
    const SUBMIT = 'mining.submit';

    /**
     * @var Connection
     */
    private $conn;

    /**
     * MiningServer constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->conn = $connection;
    }

    /**
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function subscribeMining()
    {
        return $this->conn->request(self::MINING_SUBSCRIBE);
    }

    /**
     * @param string $user
     * @param string $password
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function authorize($user, $password)
    {
        return $this->conn->request(self::AUTHORIZE, [$user, $password]);
    }

    /**
     * @param string $worker_name
     * @param string $job_id
     * @param int $extranonce2
     * @param int $ntime
     * @param int $nonce
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function submit($worker_name, $job_id, $extranonce2, $ntime, $nonce)
    {
        return $this->conn->request(self::SUBMIT, [$worker_name, $job_id, $extranonce2, $ntime, $nonce]);
    }
}
