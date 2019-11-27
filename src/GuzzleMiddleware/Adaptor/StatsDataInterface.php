<?php

namespace EmagTechLabs\GuzzleMiddleware\Adaptor;

interface StatsDataInterface
{

    public function timing(string $key, float $time);


    public function gauge(string $key, float $value);


    public function set(string $key, float $value);


    public function increment(string $key);


    public function decrement(string $key);

}