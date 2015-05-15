<?php

require_once "vendor/autoload.php";


use \BitWasp\Bitcoin\Stratum\StratumClient;
use \BitWasp\Bitcoin\Stratum\Request\RequestFactory;

$requestFactory = new RequestFactory;

$host = 'electrum3.hachre.de';
$port = 50001;

// Initialize react event loop, resolver, and connector
$loop = React\EventLoop\Factory::create();
$dnsResolverFactory = new React\Dns\Resolver\Factory();
$dns = $dnsResolverFactory->create('8.8.8.8', $loop);
$connector = new \React\SocketClient\Connector($loop, $dns);

// Initialize TCP transport for stratum client
$tcp = new \BitWasp\Bitcoin\Stratum\Connector\Tcp($connector, $host, $port);
$stratum = new StratumClient($tcp, $requestFactory);

// Make the query, receive a Promise
$t = $stratum->request('blockchain.transaction.get', ['2439243c47803a613728beab5ccfd7a426c9bfdd069d463b28f6f49915801988']);
$t->then(function (\BitWasp\Bitcoin\Stratum\Request\Response $response) {
    var_dump($response);
});

$loop->run();
