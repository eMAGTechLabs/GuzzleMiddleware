<?php

namespace EmagTechLabs\GuzzleMiddleware;

use GuzzleHttp\TransferStats;
use Psr\Http\Message\RequestInterface;
use EmagTechLabs\GuzzleMiddleware\Helper\ProfilerHelper;
use EmagTechLabs\GuzzleMiddleware\Adapter\StatsDataInterface;


class HttpCodeProfiler
{
    use ProfilerHelper;

    /** @var StatsDataInterface */
    private $statsdService;

    /** @var string */
    private $statsdKey;

    public function __construct(StatsDataInterface $statsdService)
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
        $this->statsdService->increment($this->getKey($stats));
    }

    private function getKey(TransferStats $stats): string
    {
        $key = ($this->statsdKey ?? $this->generateKey($stats));
        $key .= '.' . $stats->getResponse()->getStatusCode();

        return $key;
    }
}
