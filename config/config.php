<?php

use Zend\ConfigAggregator\ArrayProvider;
use Zend\ConfigAggregator\ConfigAggregator;

// Using a self-called anonymous function to create its own scope and keep the variables away from the global scope.
return (function () {
    $cacheConfig = [
        'config_cache_path' => __DIR__ . '/data/config-cache.php',
    ];

    $aggregator = new ConfigAggregator(
        [
            new ArrayProvider($cacheConfig),

            // Default App module config
            \TravelSorter\App\ConfigProvider::class,

            new \Zend\ConfigAggregator\PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
        ], $cacheConfig['config_cache_path']
    );

    $mergedConfig = $aggregator->getMergedConfig();

    return $mergedConfig;
})();
