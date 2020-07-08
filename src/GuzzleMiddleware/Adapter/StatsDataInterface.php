<?php

namespace EmagTechLabs\GuzzleMiddleware\Adapter;

interface StatsDataInterface
{

    public function timing(string $key, float $time): void;


    public function gauge(string $key, float $value): void;


    public function set(string $key, float $value): void;


    public function increment(string $key): void;


    public function decrement(string $key): void;

}
