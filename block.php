<?php

require_once "vendor/autoload.php";


use BitWasp\Stratum\Request\RequestFactory;
use BitWasp\Stratum\Request\Response;
use BitWasp\Stratum\Factory;

$host = 'bitcoin.trouth.net';
$port = 50001;

// Initialize react event loop, resolver, and connector
$loop = React\EventLoop\Factory::create();
$connector = new \React\SocketClient\Connector(
    $loop,
    (new React\Dns\Resolver\Factory())->create('8.8.8.8', $loop)
);

$requestFactory = new RequestFactory;
$clientFactory = new Factory($loop, $connector, $requestFactory);
$stratum = $clientFactory->create($host, $port);

$v = $stratum->request('server.version', ['1.9.7', ' 0.6'])->then(function (Response $r) {
    echo "Server version: " . $r->getResult() . "\n";
});

// Make the query, receive a Promise
$t = $stratum->request('blockchain.address.get_balance', ['1NfcqVqW4f6tACwaqjyKXRV75aqt3VEVPE']);
$t->then(function (Response $response) {
    var_dump($response);
}, function (\BitWasp\Stratum\Exceptions\ApiError $error) {
    echo sprintf(" [id: %s] error: %s", $error->getId(), $error->getMessage());
}, function () {

});

$loop->run();
