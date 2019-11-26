<?php

namespace EmagTechLabs\GuzzleMiddleware;

use GuzzleHttp\TransferStats;
use Psr\Http\Message\RequestInterface;
use EmagTechLabs\GuzzleMiddleware\Helper\ProfilerHelper;
use Liuggio\StatsdClient\Factory\StatsdDataFactoryInterface;


class HttpCodeProfiler
{
    use ProfilerHelper;

    /** @var StatsdDataFactoryInterface */
    private $statsdService;

    /** @var string */
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
        $this->statsdService->increment($this->getKey($stats));
        $this->statsdService->flush();
    }

    private function getKey(TransferStats $stats): string
    {
        $key = ($this->statsdKey ?? $this->generateKey($stats));
        $key .= '.' . $stats->getResponse()->getStatusCode();

        return $key;
    }
}