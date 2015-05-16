<?php

namespace BitWasp\Stratum\Request;

class Request
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $params = [];

    /**
     * @param string $method
     * @param array $params
     */
    public function __construct($id, $method, array $params = array())
    {
        $this->id = $id;
        $this->method = $method;
        $this->params = $params;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function write()
    {
        return json_encode(
            [
                "json-rpc" => "2.0",
                "id" => $this->id,
                "method" => $this->method,
                "params" => $this->params
            ]
        ) . "\n";
    }
}
