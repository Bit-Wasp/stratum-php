<?php

namespace BitWasp\Bitcoin\Stratum\Connector;

use BitWasp\Bitcoin\Stratum\Request\Request;

interface ConnectorInterface
{
    /**
     * @param \BitWasp\Bitcoin\Stratum\Request\Request $request
     * @return \React\Promise\Promise
     */
    public function send(Request $request);
}
