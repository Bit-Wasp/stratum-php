<?php

namespace BitWasp\Stratum;

use BitWasp\Stratum\Api\ElectrumClient;
use BitWasp\Stratum\Api\MiningClient;
use BitWasp\Stratum\Notification\AddressNotification;
use BitWasp\Stratum\Notification\HeadersNotification;
use BitWasp\Stratum\Notification\MiningNotification;
use BitWasp\Stratum\Notification\NotificationInterface;
use BitWasp\Stratum\Notification\NumBlocksNotification;
use BitWasp\Stratum\Notification\SetDifficultyNotification;
use BitWasp\Stratum\Request\Request;
use BitWasp\Stratum\Request\RequestFactory;
use Evenement\EventEmitter;
use React\Promise\Deferred;
use React\Stream\Stream;

class Connection extends EventEmitter
{
    /**
     * @var Stream
     */
    private $stream;
    
    /**
     * @var RequestFactory
     */
    private $factory;
    
    /**
     * @var Deferred[]
     */
    private $deferred = [];

    /**
     * @var string
     */
    private $streamBuffer = '';

    /**
     * Connection constructor.
     * @param Stream $stream
     * @param RequestFactory $requestFactory
     */
    public function __construct(Stream $stream, RequestFactory $requestFactory)
    {
        $this->factory = $requestFactory;
        $this->stream = $stream;
        $this->stream->on('data', [$this, 'onData']);
    }

    public function close()
    {
        return $this->stream->close();
    }

    /**
     * @param string $data
     * @return bool|void
     */
    public function sendData($data)
    {
        return $this->stream->write($data);
    }

    /**
     * @param NotificationInterface $notification
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function sendNotify(NotificationInterface $notification)
    {
        return $this->sendData($notification->toRequest()->write());
    }
    
    /**
     * @param Request $request
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function sendRequest(Request $request)
    {
        $result = new Deferred();
        $this->deferred[$request->getId()] = $result;
        $this->sendData($request->write());
        return $result->promise();
    }

    /**
     * @param string $method
     * @param array $params
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function request($method, array $params = [])
    {
        $request = $this->factory->create($method, $params);
        return $this->sendRequest($request);
    }
    
    /**
     * @param string $data
     */
    public function onData($data)
    {
        $buffer = $this->streamBuffer . $data;

        while (($nextPos = strpos($buffer, "\n"))) {
            $msg = substr($buffer, 0, $nextPos);
            $buffer = substr($buffer, $nextPos);
            if (substr($buffer, -1) == "\n") {
                $buffer = substr($buffer, 1);
            }
            $this->onMessage($msg);
        }

        if (!$buffer) {
            $this->streamBuffer = '';
        } else {
            $this->streamBuffer = $buffer;
        }
    }

    /**
     * @param string $data
     * @throws \BitWasp\Stratum\Exception\ApiError
     * @throws \Exception
     */
    public function onMessage($data)
    {
        $response = $this->factory->response($data);
        if (isset($this->deferred[$response->getId()])) {
            $this->deferred[$response->getId()]->resolve($response);
        } else {
            $this->emit('message', [$response]);

            if ($response instanceof Request) {
                $params = $response->getParams();

                switch ($response->getMethod()) {
                    case ElectrumClient::HEADERS_SUBSCRIBE:
                        if (!isset($params[0])) {
                            throw new \RuntimeException('Headers notification missing body');
                        }
                        
                        $header = $params[0];
                        if (count($header) !== 8) {
                            throw new \RuntimeException('Headers notification missing parameter');
                        }

                        $this->emit(ElectrumClient::HEADERS_SUBSCRIBE, [new HeadersNotification($header[0], $header[1], $header[2], $header[3], $header[4], $header[5], $header[6], $header[7])]);
                        break;
                    case ElectrumClient::ADDRESS_SUBSCRIBE:
                        if (!isset($params[0]) || !isset($params[1])) {
                            throw new \RuntimeException('Address notification missing address/txid');
                        }

                        $this->emit(ElectrumClient::ADDRESS_SUBSCRIBE, [new AddressNotification($params[0], $params[1])]);
                        break;
                    case ElectrumClient::NUMBLOCKS_SUBSCRIBE:
                        if (!isset($params[0])) {
                            throw new \RuntimeException('Missing notification parameter: height');
                        }

                        $this->emit(ElectrumClient::NUMBLOCKS_SUBSCRIBE, [new NumBlocksNotification($params[0])]);
                        break;
                    case MiningClient::SET_DIFFICULTY:
                        if (!isset($params[0])) {
                            throw new \RuntimeException('Missing mining difficulty notification parameter');
                        }

                        $this->emit(MiningClient::SET_DIFFICULTY, [new SetDifficultyNotification($params[0])]);
                        break;
                    case MiningClient::NOTIFY:
                        if (count($params) !== 9) {
                            throw new \RuntimeException('Missing mining notification parameter');
                        }

                        $this->emit(MiningClient::NOTIFY, [new MiningNotification($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7], $params[8])]);
                        break;
                }
            }
        }
    }
}
