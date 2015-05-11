<?php

namespace BitWasp\Bitcoin\Stratum\Request;

class RequestFactory
{
    /**
     * @var array
     */
    private $nonces = [];

    /**
     * @param $method
     * @param array $params
     * @return Request
     */
    public function create($method, $params = array())
    {
        do {
            $random = mt_rand(0, PHP_INT_MAX);
        } while (in_array($random, $this->nonces));

        return new Request($random, $method, $params);
    }
}
