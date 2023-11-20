<?php

declare(strict_types = 1);

use Rector\Config\RectorConfig;
use Rector\Transform\Rector\Class_\AddAllowDynamicPropertiesAttributeRector;

return static function (RectorConfig $rectorConfig): void {
    // Required to fix an issue with IDE Helper Generator Model mixin generation with PHP8.2
    // Can be removed if https://github.com/barryvdh/laravel-ide-helper/pull/1428 is merged

    $rectorConfig->paths([
        __DIR__ . '/_ide_helper_models.php',
    ]);

    // register a single rule
    $rectorConfig->rule(AddAllowDynamicPropertiesAttributeRector::class);

    // define sets of rules
    //    $rectorConfig->sets([
    //        LevelSetList::UP_TO_PHP_82
    //    ]);
};
