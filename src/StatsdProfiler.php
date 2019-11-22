<?php

namespace App\Command;

use GuzzleHttp\TransferStats;
use Liuggio\StatsdClient\Factory\StatsdDataFactoryInterface;
use Psr\Http\Message\RequestInterface;

class StatsdProfiler
{
    /** @var StatsdDataFactoryInterface */
    private $statsdService;

    private $statsdKey;

    public function __construct(StatsdDataFactoryInterface $statsdService)
    {
        $this->statsdService = $statsdService;
    }

    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $options['on_stats'] = function (TransferStats $stats) {
                $this->startProfiling($stats);
            };

            return $handler($request, $options);
        };

    }

    public function setKey($key): void
    {
        $this->statsdKey = $key;
    }

    private function startProfiling(TransferStats $stats): void
    {
        $this->statsdService->timing($this->generateKey($stats), $stats->getTransferTime());
        $this->statsdService->flush();
    }

    private function generateKey(TransferStats $stats): string
    {
        return $this->statsdKey ?? $stats->getEffectiveUri();
    }
}