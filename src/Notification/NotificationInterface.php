<?php

namespace BitWasp\Stratum\Notification;

use BitWasp\Stratum\Request\Request;

interface NotificationInterface
{
    /**
     * @return Request
     */
    public function toRequest();
}
