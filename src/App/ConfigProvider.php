<?php

namespace TravelSorter\App;

use TravelSorter\App\BasePathDetector\BasePathDetectorInterface;
use TravelSorter\App\Dispatcher\DispatcherAggregate;
use TravelSorter\App\Dispatcher\DispatcherAggregateFactory;
use TravelSorter\App\Dispatcher\DispatcherInterface;

/**
 * The configuration provider for the App module
 */
class ConfigProvider
{
    public function __invoke()
    {
        return [
            'container_definitions' => $this->getContainerDefinitions(),
            self::class => [
                BasePathDetectorInterface::class => $this->getBasePathDetectorConfig(),
                DispatcherInterface::class => $this->getDispatcherConfig(),
            ]
        ];
    }

    public function getContainerDefinitions(): array
    {
        return [
            BasePathDetector\BasePathDetectorInterface::class => \DI\factory(BasePathDetector\BasePathDetectorFactory::class),
            Dispatcher\DispatcherInterface::class => \DI\factory(DispatcherAggregateFactory::class),
        ];
    }

    public function getBasePathDetectorConfig(): array
    {
        return [
            BasePathDetectorInterface::CONFIG_PUBLIC_DIRECTORY => realpath(__DIR__ . '/../../public'),
            BasePathDetectorInterface::CONFIG_DOCUMENT_ROOT => $_SERVER['DOCUMENT_ROOT'],
        ];
    }

    public function getDispatcherConfig(): array
    {
        return [
            DispatcherInterface::CONFIG_DISPATCHERS => [],
        ];
    }

}
