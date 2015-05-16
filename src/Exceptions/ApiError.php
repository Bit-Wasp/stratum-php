<?php

namespace BitWasp\Stratum\Exceptions;

class ApiError extends \Exception
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     * @param int $error
     */
    public function __construct($id, $error)
    {
        $this->id = $id;
        parent::__construct($error);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
