<?php

namespace BitWasp\Bitcoin\Stratum;

use BitWasp\Bitcoin\Stratum\Connector\ConnectorInterface;
use BitWasp\Bitcoin\Stratum\Request\Request;
use BitWasp\Bitcoin\Stratum\Request\RequestFactory;

class StratumClient
{
    /**
     * @var RequestFactory
     */
    private $reqFactory;

    /**
     * @var ConnectorInterface
     */
    private $connector;

    /**
     * @param \BitWasp\Bitcoin\Stratum\Connector\ConnectorInterface $connector
     * @param RequestFactory $reqFactory
     */
    public function __construct(ConnectorInterface $connector, RequestFactory $reqFactory)
    {
        $this->connector = $connector;
        $this->reqFactory = $reqFactory;
    }

    /**
     * @param $method
     * @param array $params
     * @return \React\Promise\Promise
     */
    public function request($method, array $params = [])
    {
        return $this->connector->send($this->reqFactory->create($method, $params))
            ->then(function ($request) {
                return $this->reqFactory->response($request);
            });
    }
}
