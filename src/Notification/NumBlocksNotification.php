<?php

namespace BitWasp\Stratum\Notification;

use BitWasp\Stratum\Api\ElectrumClient;
use BitWasp\Stratum\Request\Request;

class NumBlocksNotification implements NotificationInterface
{
    /**
     * @var int
     */
    private $height;

    /**
     * NumBlocksNotification constructor.
     * @param int $height
     */
    public function __construct($height)
    {
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return Request
     */
    public function toRequest()
    {
        return new Request(null, ElectrumClient::NUMBLOCKS_SUBSCRIBE, [$this->height]);
    }
}
