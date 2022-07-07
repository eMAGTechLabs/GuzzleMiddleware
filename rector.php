<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src'
    ]);

    $rectorConfig->sets([SetList::CODE_QUALITY]);

    // register single rule
    $rectorConfig->rule(TypedPropertyRector::class);

    // is your PHP version different from the one you refactor to? [default: your PHP version], uses PHP_VERSION_ID format
    $rectorConfig->phpVersion(PhpVersion::PHP_81);

    // Path to PHPStan with extensions, that PHPStan in Rector uses to determine types
    $rectorConfig->phpstanConfig(__DIR__ . '/phpstan.neon.dist');
};
