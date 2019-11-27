<?php

namespace EmagTechLabs\GuzzleMiddleware\Adapter;

use Domnikl\Statsd\Client;

class DominikAdapter implements StatsDataInterface
{
    /** @var Client */
    private $statsdClient;

    public function __construct(Client $statsdClient)
    {
        $this->statsdClient = $statsdClient;
    }

    public function timing(string $key, float $time): void
    {
        $this->statsdClient->timing($key, $time);
    }

    public function gauge(string $key, float $value): void
    {
        $this->statsdClient->gauge($key, $value);
    }

    public function set(string $key, float $value): void
    {
        $this->statsdClient->set($key, $value);
    }

    public function increment(string $key): void
    {
        $this->statsdClient->increment($key);
    }

    public function decrement(string $key): void
    {
        $this->statsdClient->decrement($key);
    }
}
