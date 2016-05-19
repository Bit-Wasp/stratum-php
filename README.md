## stratum-php
[![Build Status](https://travis-ci.org/Bit-Wasp/stratum-php.svg?branch=master)](http://travis-ci.org/Bit-Wasp/stratum-php)
[![Code Coverage](https://scrutinizer-ci.com/g/bit-wasp/stratum-php/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/bit-wasp/stratum-php/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Bit-Wasp/stratum-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Bit-Wasp/stratum-php/?branch=master)
 
Implementation of the Stratum protocol (for electrum and mining) using ReactPHP

### Client

The Client class is used to make a connection to a host. It takes a `ConnectorInterface`
 and `RequestFactory`. 
 
 `react/socket-client` provides a number of connectors, which can be combined
 to produce the desired functionality. 

```php
use \BitWasp\Stratum\Client;
use \BitWasp\Stratum\Connection;
use \BitWasp\Stratum\Request\RequestFactory;

$loop = \React\EventLoop\Factory::create();

$resolver = new \React\Dns\Resolver\Factory();

// Raw TCP, cannot perform DNS resolution
$tcp = new \React\SocketClient\TcpConnector($loop);

// TCP Connector with a DNS resolver
$dns = new \React\SocketClient\DnsConnector($tcp, $resolver->create('8.8.8.8', $loop));

// Encrypted connection
$context_options = [];

$tls = new \React\SocketClient\SecureConnector($dns, $loop, $context_options);

$requests = new RequestFactory;
$client = new Client($tls, $requests);

$host = '';
$port = '';

$client->connect($host, $port)->then(function (Connection $conn) {
    /* success */
}, function (\Exception $e) {
    /*  error  */
    print_r($e->getMessage());
});

$loop->run();
```

The SecureConnector initiates a TLS session to encrypt your connection. $context_options is an optional
value, but many Electrum servers have misconfigured SSL certificates! (incorrect CN field, or are self-signed)
These will not be accepted with the default verification settings, and can be disabled by changing the $context_options
``` 
$context_options = ["verify_name" => false, "allow_self_signed" => true];
```

### Connection

A `Connection` represents a connection to a peer. 

Requests can be sent to the peer using `Connection::request($method, $params = [])`,
which returns a Promise for the pending result. When a response with the same ID is
received, the promise will resolve this as the result. 

```php
$conn->request('server.banner')->then(function (Response $response) {
    print_r($response->getResult());
}, function (\Exception $e) {
    echo $e->getMessage();
});
```

`Request` instances can be sent using `Connection::sendRequest(Request $request)`
which also returns a promise. 

For a list of methods for the electrum and mining clients, see the respective Api classes.
The constants are method's for these APIs.

```php
$conn->sendRequest(new Request(null, 'server.banner'))->then(function (Response $response) {
    print_r($response->getResult());
}, function (\Exception $e) {
    echo $e->getMessage();
});
```

`NotificationInterface`'s can be sent using `Connection::sendNotify(NotificationInterface $note)`
Notifications are not requests, and don't receive a response. This method is only relevant if 
using `Connection` from a servers perspective.  

```php
$conn->sendNotification(new NumBlocksNotification(123123));
```

#### Api's

The Stratum protocol is implemented by electrum servers and stratum mining pools. 
Their methods are exposed by `ElectrumClient` and `MiningClient` respectively.

The api methods cause a Request to be sent, returning a promise to capture the result.

```php
use \BitWasp\Stratum\Api\ElectrumClient;
use \BitWasp\Stratum\Client;
use \BitWasp\Stratum\Connection;
use \BitWasp\Stratum\Request\Response;
use \BitWasp\Stratum\Request\RequestFactory;

$loop = \React\EventLoop\Factory::create();
$tcp = new \React\SocketClient\TcpConnector($loop)   ;

$resolver = new \React\Dns\Resolver\Factory();
$dns = new \React\SocketClient\DnsConnector($tcp,$resolver->create('8.8.8.8', $loop));
$tls = new \React\SocketClient\SecureConnector($dns, $loop);
$requests = new RequestFactory;
$client = new Client($tls, $requests);

$host = 'anduck.net';
$port = 50002;
$client->connect($host, $port)->then(function (Connection $conn) {
    $electrum = new ElectrumClient($conn);
    $electrum->addressListUnspent('1NS17iag9jJgTHD1VXjvLCEnZuQ3rJDE9L')->then(function (Response $response) {
        print_r($response->getResult());
    });
}, function (\Exception $e) {
    echo 'error';
    echo $e->getMessage().PHP_EOL;
    /*  error  */
});

$loop->run();
```


#### Events

`Connection` emits a `message` event when a message is received which 
was not initiated by a Request. These messages are typically due to subscriptions.

The following events are emitted automatically by the library when encountered.
The event name is the method used to enable the subscription. 
  - 'blockchain.headers.subscribe' emits a `HeadersNotification`
  - 'blockchain.address.subscribe' emits a `AddressNotification`
  - 'blockchain.numblocks.subscribe' emits a `NumBlocksNotification`
  - 'mining.subscribe' emits a `MiningNotification`
  - 'mining.set_difficulty' emits a `SetDifficultyNotification` 

```php
use \BitWasp\Stratum\Api\ElectrumClient;
use \BitWasp\Stratum\Client;
use \BitWasp\Stratum\Connection;
use \BitWasp\Stratum\Notification\AddressNotification;
use \BitWasp\Stratum\Request\RequestFactory;

$loop = React\EventLoop\Factory::create();
$tcp = new \React\SocketClient\TcpConnector($loop);
$resolver = new \React\Dns\Resolver\Factory();
$dns = new \React\SocketClient\DnsConnector($tcp, $resolver->create('8.8.8.8', $loop));
$tls = new \React\SocketClient\SecureConnector($dns, $loop);

$requests = new RequestFactory;
$client = new Client($tls, $requests);

$host = 'anduck.net';
$port = 50002;
$client->connect($host, $port)->then(function (Connection $conn) {
    $conn->on('message', function ($message) {
        echo "Message received: ".PHP_EOL;
        print_r($message);
    });

    $conn->on(ElectrumClient::ADDRESS_SUBSCRIBE, function (AddressNotification $address) {
        echo "Received address update\n";
    });

    $electrum = new ElectrumClient($conn);
    $electrum->subscribeAddress('1NS17iag9jJgTHD1VXjvLCEnZuQ3rJDE9L')->then(function () {
        echo "subscribed\n";
    });
}, function (\Exception $e) {
    echo "ERROR: " . $e->getMessage().PHP_EOL;
});

$loop->run();
```

### Further Information

  - http://docs.electrum.org/en/latest/protocol.html
  - https://electrum.orain.org/wiki/Stratum_protocol_specification
