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

    /** @var string|null */
    private $statsdKey = null;

    public function __construct(StatsDataInterface $statsdService)
    {
        $this->statsdService = $statsdService;
    }

    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $onStats = $options['on_stats'] ?? null;

            $options['on_stats'] = function (TransferStats $stats) use ($onStats) {
                if (is_callable($onStats)) {
                    $onStats($stats);
                }

                $this->startProfiling($stats);
            };

            return $handler($request, $options);
        };

    }

    public function setKey(string $key): void
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
        $statusCode = (empty($stats->getResponse())) ? '' : $stats->getResponse()->getStatusCode();

        return $key . ('.' . $statusCode);
    }
}
