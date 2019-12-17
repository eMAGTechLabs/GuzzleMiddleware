<?php

namespace EmagTechLabs\GuzzleMiddleware;

use GuzzleHttp\TransferStats;
use Psr\Http\Message\RequestInterface;
use EmagTechLabs\GuzzleMiddleware\Helper\ProfilerHelper;
use EmagTechLabs\GuzzleMiddleware\Adapter\StatsDataInterface;

class TimingProfiler
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
            $onStats = null;
            if (isset($options['on_stats']) && is_callable($options['on_stats'])) {
                $onStats = $options['on_stats'];
            }

            $options['on_stats'] = function (TransferStats $stats) use ($onStats) {
                if (is_callable($onStats)) {
                    $onStats($stats);
                }

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
        return ($this->statsdKey ?? $this->generateKey($stats));
    }

    private function startProfiling(TransferStats $stats): void
    {
        $x = $stats->getTransferTime();
        $this->statsdService->timing($this->getKey($stats), $stats->getTransferTime());
    }
}
