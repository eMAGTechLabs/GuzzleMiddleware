<?php

namespace EmagTechLabs\GuzzleMiddleware;

use GuzzleHttp\TransferStats;
use Liuggio\StatsdClient\Factory\StatsdDataFactoryInterface;
use Psr\Http\Message\RequestInterface;

class TimingProfiler
{
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

    public function generateKey(TransferStats $stats): string
    {
        $host = parse_url($stats->getEffectiveUri(), PHP_URL_HOST);
        if($host !== null) {
            $urlHost = str_replace('.', '_', $host);
        }

        $urlPath = str_replace('.', '_', parse_url($stats->getEffectiveUri(), PHP_URL_PATH));

        $parsedUrl = $urlPath;
        if(isset($urlHost)) {
            $parsedUrl = $urlHost . '_' . $urlPath;
        }

        return ($this->statsdKey ?? $parsedUrl);
    }

    private function startProfiling(TransferStats $stats): void
    {
        $this->statsdService->timing($this->generateKey($stats), $stats->getTransferTime());
        $this->statsdService->flush();
    }
}