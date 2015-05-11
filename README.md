## stratum-php
[![Build Status](https://travis-ci.org/Bit-Wasp/stratum-php.svg?branch=master)](http://travis-ci.org/Bit-Wasp/stratum-php)
[![Code Coverage](https://scrutinizer-ci.com/g/bit-wasp/stratum-php/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/bit-wasp/stratum-php/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Bit-Wasp/stratum-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Bit-Wasp/stratum-php/?branch=master)
 
Implementation of the Stratum protocol (for electrum and mining) using ReactPHP

Currently this library supports a TCP transport to stratum servers. 
Examples of these and other servers can be found on the Electrum server list.

Stratum enables rather stateless wallets to be built with minimal effort, and depending on the use-case could replace running a full node.
 
### Example
```php

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
$t = $stratum->getTransaction('2439243c47803a613728beab5ccfd7a426c9bfdd069d463b28f6f49915801988');
$t->then(function ($response) {
    echo $response;
});

$loop->run();

```