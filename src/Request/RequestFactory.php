<?php

namespace BitWasp\Stratum\Request;

use BitWasp\Stratum\Exceptions\ApiError;

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

    /**
     * @param $string
     * @return Response
     * @throws \Exception
     */
    public function response($string)
    {
        $decoded = json_decode(trim($string), true);

        if (json_last_error() === JSON_ERROR_NONE) {
            if (!isset($decoded['id'])) {
                throw new \Exception('Response missing id');
            }

            if (isset($decoded['error'])) {
                throw new ApiError($decoded['id'], $decoded['error']);
            } elseif ($decoded['result']) {
                return new Response($decoded['id'], $decoded['result']);
            }

            throw new \Exception('Response missing error or result');
        }

        throw new \Exception('Invalid Json received');
    }
}
