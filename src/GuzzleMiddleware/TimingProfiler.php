<?php

namespace EmagTechLabs\GuzzleMiddleware;


use GuzzleHttp\TransferStats;
use Psr\Http\Message\RequestInterface;
use EmagTechLabs\GuzzleMiddleware\Helper\ProfilerHelper;
use Liuggio\StatsdClient\Factory\StatsdDataFactoryInterface;

class TimingProfiler
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

    public function getKey(TransferStats $stats): string
    {
        return ($this->statsdKey ?? $this->getKey($stats));
    }

    private function startProfiling(TransferStats $stats): void
    {
        $this->statsdService->timing($this->getKey($stats), $stats->getTransferTime());
        $this->statsdService->flush();
    }
}