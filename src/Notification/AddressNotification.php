<?php

namespace BitWasp\Stratum\Notification;

use BitWasp\Stratum\Api\ElectrumClient;
use BitWasp\Stratum\Request\Request;

class AddressNotification implements NotificationInterface
{
    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $txid;

    /**
     * AddressNotification constructor.
     * @param string $address
     * @param string $txid
     */
    public function __construct($address, $txid)
    {
        $this->address = $address;
        $this->txid = $txid;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getTxid()
    {
        return $this->txid;
    }

    /**
     * @return Request
     */
    public function toRequest()
    {
        return new Request(null, ElectrumClient::ADDRESS_SUBSCRIBE, [$this->address, $this->txid]);
    }
}
