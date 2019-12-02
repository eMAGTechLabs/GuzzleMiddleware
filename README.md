# Guzzle 6 Middleware
This is a Guzzle 6 middleware, which allows you to profile an http call, and send information to statsd daemon.
## Requirements

### Dependencies

| Dependency | Version 
|:--- |:---:|
| **`php`** | ^7.0 |
| **`guzzlehttp/guzzle`** | ^6.0 | 
| **`liuggio/statsd-php-client`** | ^1.0 | 
| **`domnikl/statsd`** | ^2.0 |

## Installation

This library is installed via [`composer`](http://getcomposer.org).

```bash
composer require "emag-tech-labs/guzzle-middleware"
```

## Usage
The package is able work with 2 different statsd libraries, illugio or dominikl. Based on you're choice you will have to use the right adapter (DominikAdapter or IlugioAdapter) in order to instantiate the statsd client.  

## Example


```php
$statsdClient = new DominikAdapter($dominikStatsdClient);
$handlerStack = new HandlerStack();
$handlerStack->push(new TimingProfiler($statsdClient));
$handlerStack->push(new HttpCodeProfiler($statsdClient));

$client = new Client(['handler' => $handlerStack]);
```

 
