<?php

namespace BitWasp\Stratum\Notification;

use BitWasp\Stratum\Api\MiningClient;
use BitWasp\Stratum\Request\Request;

class SetDifficultyNotification implements NotificationInterface
{
    /**
     * @var int|string
     */
    private $difficulty;

    /**
     * SetDifficultyNotification constructor.
     * @param int|string $difficulty
     */
    public function __construct($difficulty)
    {
        $this->difficulty = $difficulty;
    }

    /**
     * @return int|string
     */
    public function getDifficulty()
    {
        return $this->difficulty;
    }

    /**
     * @return Request
     */
    public function toRequest()
    {
        return new Request(null, MiningClient::SET_DIFFICULTY, [$this->difficulty]);
    }
}
