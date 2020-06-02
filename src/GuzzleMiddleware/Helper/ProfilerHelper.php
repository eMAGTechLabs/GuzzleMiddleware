<?php
namespace EmagTechLabs\GuzzleMiddleware\Helper;

use GuzzleHttp\TransferStats;

trait ProfilerHelper {

    public function generateKey(TransferStats $stats): string
    {
        $host = parse_url($stats->getEffectiveUri(), PHP_URL_HOST);
        if(!empty($host)) {
            $urlHost = str_replace('.', '_', $host);
        }

        $urlPath = parse_url($stats->getEffectiveUri(), PHP_URL_PATH);
        if(!empty($urlPath)) {
            $urlPath = str_replace('.', '_', $urlPath);
        }
        $parsedUrl = (empty($urlPath)) ? '' : $urlPath;
        if(isset($urlHost)) {
            $parsedUrl = $urlHost . '_' . $urlPath;
        }

        return $parsedUrl;
    }
}