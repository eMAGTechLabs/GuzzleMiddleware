<?php

namespace EmagTechLabs\GuzzleMiddleware\Adapter;

use Liuggio\StatsdClient\Service\StatsdService;

class IlugioAdapter implements StatsDataInterface
{
    /** @var StatsdService */
    private $illugioService;

    public function __construct(StatsdService $illugioService)
    {
        $this->illugioService = $illugioService;
    }

    public function timing(string $key, float $time): void
    {
        $this->illugioService->timing($key, $time);
        $this->illugioService->flush();
    }

    public function gauge(string $key, float $value): void
    {
        $this->illugioService->gauge($key, $value);
        $this->illugioService->flush();
    }

    public function set(string $key, float $value): void
    {
        $this->illugioService->set($key, $value);
        $this->illugioService->flush();
    }

    public function increment(string $key): void
    {
        $this->illugioService->increment($key);
        $this->illugioService->flush();
    }

    public function decrement(string $key): void
    {
        $this->illugioService->decrement($key);
        $this->illugioService->flush();
    }
}
