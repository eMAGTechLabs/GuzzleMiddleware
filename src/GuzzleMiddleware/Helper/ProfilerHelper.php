<?php
namespace EmagTechLabs\GuzzleMiddleware\Helper;

use GuzzleHttp\TransferStats;

trait ProfilerHelper {

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

        return $parsedUrl;
    }
}