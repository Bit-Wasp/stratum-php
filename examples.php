<?php

require_once "vendor/autoload.php";

use \BitWasp\Bitcoin\Stratum\StratumClient;

$requestFactory = new \BitWasp\Bitcoin\Stratum\Request\RequestFactory();

$h = 'electrum3.hachre.de';
$port = 50001;

$loop = React\EventLoop\Factory::create();

$dnsResolverFactory = new React\Dns\Resolver\Factory();
$dns = $dnsResolverFactory->create('8.8.8.8', $loop);

$connector = new \React\SocketClient\Connector($loop, $dns);
$tcp = new \BitWasp\Bitcoin\Stratum\Connector\Tcp($connector, $h, $port);

$stratum = new StratumClient($tcp, $requestFactory);
/*
$t = $stratum->getTransaction('2439243c47803a613728beab5ccfd7a426c9bfdd069d463b28f6f49915801988');
$t->then(function ($response) {
        echo $response;
    },
    function () { echo 'boo'; },
    function () { echo 'bah'; }
);

$b = $stratum->getBanner();
$b->then(function ($response) {
    echo $response;
},
    function () { echo 'boo'; },
    function () { echo 'bah'; }
);

$c = $stratum->sendVersion('1.9.7', '0.6');
$c->then(function ($response) {
    echo $response;
},
    function () { echo 'boo'; },
    function () { echo 'bah'; }
);

$d = $stratum->getAddressHistory('1NS17iag9jJgTHD1VXjvLCEnZuQ3rJDE9L');
$d->then(function ($response) {
    echo $response;
},
    function () { echo 'boo'; },
    function () { echo 'bah'; }
);

$e = $stratum->getTransactionMerkle('2439243c47803a613728beab5ccfd7a426c9bfdd069d463b28f6f49915801988', 347315);
$e->then(function ($response) {
    echo $response;
},
    function () { echo 'boo'; },
    function () { echo 'bah'; }
);
*/


/*
$f = $stratum->getNodeVersion();
$f->then(function ($response) {
    echo $response;
},
    function () { echo 'boo'; },
    function () { echo 'bah'; }
);

$f = $stratum->getServices();
$f->then(function ($response) {
    echo $response;
},
    function () { echo 'boo'; },
    function () { echo 'bah'; }
);*/

$loop->run();