<?php

namespace BitWasp\Stratum;

use BitWasp\Stratum\Request\RequestFactory;

class Client
{
    /**
     * @var Executor
     */
    private $executor;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @param Executor $executor
     * @param RequestFactory $requestFactory
     */
    public function __construct(Executor $executor, RequestFactory $requestFactory)
    {
        $this->executor = $executor;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @param $method
     * @param array $params
     * @return \React\Promise\Promise
     */
    public function request($method, array $params = [])
    {
        return $this->executor->query($this->requestFactory->create($method, $params));
    }
}
